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
    $before = null;
    if (file_exists($tokenPath)) {
        $before = json_decode(file_get_contents($tokenPath), true);
        if (!is_array($before)) {
            throw new RuntimeException('Invalid token file.');
        }
    } else {
        throw new RuntimeException('Token file not found.');
    }

    $refresher = new TokenRefresher($clientSecret, $tokenPath);
    $after = $refresher->refresh();

    if (($before['created'] ?? 0) !== ($after['created'] ?? 0)) {
        echo 'Token expired. Refreshing.' . PHP_EOL;
    }
} catch (\Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
