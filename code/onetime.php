<?php
require_once __DIR__ . '/vendor/autoload.php';

$clientSecret = __DIR__ . '/data/client_secret.json';
$client = new Google_Client();
$client->setAuthConfig($clientSecret);
$client->addScope('https://mail.google.com/'); // Full Gmail access for IMAP
$client->setAccessType('offline');
$client->setPrompt('select_account consent');
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob'); // Desktop flow

$authUrl = $client->createAuthUrl();
echo "Open the following URL in your browser:\n$authUrl\n";
echo "Enter the authorization code here: ";
$authCode = trim(fgets(STDIN));

$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
if (isset($accessToken['error'])) {
    echo "Error fetching access token: " . $accessToken['error_description'] . "\n";
    exit(1);
}

file_put_contents('token.json', json_encode($accessToken));
echo "Access token and refresh token saved to token.json\n";
