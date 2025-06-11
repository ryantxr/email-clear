<?php
require 'vendor/autoload.php';
use App\MailDeleter;
use Dotenv\Dotenv;
// IMAP server details
$host = 'imap.gmail.com';
$port = '993';

// Load credentials from the local .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$email = $_ENV['USERNAME'] ?? null;
$password = $_ENV['PASSWORD'] ?? null;

try {

    $deleter = new MailDeleter($host, $port, $email, $password, function($message){
        echo $message . "\n";
    });
    
    $deleter->delete();
} catch ( \Exception $e ) {
    echo $e->getMessage() . "\n";
}

echo "Process completed.\n";

