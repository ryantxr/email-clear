<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Webklex\PHPIMAP\ClientManager;
use App\TokenRefresher;

$host = 'imap.googlemail.com';
$port = 993;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$username = $_ENV['USERNAME'] ?? null;

$clientSecret = __DIR__.'/data/client_secret.json';
$tokenPath    = __DIR__.'/data/token.json';

try {
    $refresher = new TokenRefresher($clientSecret, $tokenPath);
    if ($refresher->needsRefresh()) {
        $token = $refresher->refresh();
    } else {
        $token = $refresher->accessToken();
    }
} catch (\Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}

$accessToken = $token['access_token'] ?? null;
if (!$accessToken) {
    echo "Access token not found." . PHP_EOL;
    exit(1);
}

$cm = new ClientManager();
$client = $cm->make([
    'host'           => $host,
    'port'           => $port,
    'encryption'     => 'ssl',
    'validate_cert'  => true,
    'protocol'       => 'imap',
    'username'       => $username,
    'password'       => $accessToken,
    'authentication' => 'oauth',
]);

try {
    $client->connect();
    $inbox = $client->getFolder('INBOX');
    $message = $inbox->messages()->all()->limit(1)->get()->first();
    if ($message) {
        echo 'Subject: ' . $message->getSubject() . PHP_EOL;
    } else {
        echo 'No messages found.' . PHP_EOL;
    }
} catch (\Throwable $e) {
    echo 'Failed: ' . $e->getMessage() . PHP_EOL;
}

