<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use GuzzleHttp\Client as HttpClient;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

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
    echo "Token expired. Refreshing." . PHP_EOL;
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