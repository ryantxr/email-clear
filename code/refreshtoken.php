<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use App\TokenRefresher;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Path to OAuth2 credentials and saved token
$clientSecret = __DIR__ . '/data/client_secret.json';
$tokenPath    = __DIR__ . '/data/token.json';

try {
    $refresher = new TokenRefresher($clientSecret, $tokenPath);
    if ( $refresher->needsRefresh() ) {
        echo 'Token expired. Refreshing.' . PHP_EOL;
        $token = $refresher->refresh();
    } else {
        $token = $refresher->accessToken();
        echo 'Token is good.' . PHP_EOL;
    }
    // $token['access_token']
} catch (\Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
