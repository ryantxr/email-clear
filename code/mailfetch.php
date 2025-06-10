<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use GuzzleHttp\Client as HttpClient;

$host = 'imap.gmail.com';
$port = '993';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$username = $_ENV['USERNAME'] ?? null;

// Path to OAuth2 credentials and saved token
$clientSecret = __DIR__ . '/data/client_secret.json';
$tokenPath    = __DIR__ . '/data/token.json';

if (!file_exists($tokenPath)) {
    echo "Token file not found." . PHP_EOL;
    exit(1);
}

$token = json_decode(file_get_contents($tokenPath), true);

if (!$token) {
    echo "Invalid token file." . PHP_EOL;
    exit(1);
}

// Refresh the token if it is expired and a refresh token is available
$expiry = ($token['created'] ?? 0) + ($token['expires_in'] ?? 0) - 60;
if (time() >= $expiry && isset($token['refresh_token'])) {
    $secret = json_decode(file_get_contents($clientSecret), true);
    $cfg = $secret['installed'] ?? $secret['web'] ?? [];
    $tokenUri = $cfg['token_uri'] ?? 'https://oauth2.googleapis.com/token';

    $http = new HttpClient();
    $response = $http->post($tokenUri, [
        'form_params' => [
            'client_id'     => $cfg['client_id'] ?? '',
            'client_secret' => $cfg['client_secret'] ?? '',
            'refresh_token' => $token['refresh_token'],
            'grant_type'    => 'refresh_token',
        ],
    ]);

    $new = json_decode($response->getBody(), true);
    if (isset($new['access_token'])) {
        $token['access_token'] = $new['access_token'];
        $token['expires_in']   = $new['expires_in'] ?? 3600;
        $token['created']      = time();
        file_put_contents($tokenPath, json_encode($token));
    }
}

// Extract the access token for IMAP authentication
$oauthToken = $token['access_token'] ?? null;

// Connect via PHP's IMAP extension using XOAUTH2
$imapPath = sprintf('{%s:%s/imap/ssl/validate-cert/auth=XOAUTH2}INBOX', $host, $port);
$connection = imap_open($imapPath, $username, $oauthToken, 0, 1, ['DISABLE_AUTHENTICATOR' => 'GSSAPI']);

if ($connection === false) {
    echo 'Failed to connect: ' . imap_last_error() . PHP_EOL;
    exit(1);
}

$emails = imap_search($connection, 'ALL');
if ($emails) {
    rsort($emails);
    $emailNumber = $emails[0];
    $overview = imap_fetch_overview($connection, $emailNumber, 0);
    $subject = $overview[0]->subject ?? '(no subject)';
    echo 'Subject: ' . $subject . PHP_EOL;
} else {
    echo 'No messages found.' . PHP_EOL;
}

imap_close($connection);

