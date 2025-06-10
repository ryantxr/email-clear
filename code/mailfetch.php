<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Webklex\PHPIMAP\ClientManager;

$host = 'imap.gmail.com';
$port = '993';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$username = $_ENV['USERNAME'] ?? null;
$password = $_ENV['PASSWORD'] ?? null;

$client = (new ClientManager())->make([
    'host'          => $host,
    'port'          => $port,
    'encryption'    => 'ssl',
    'validate_cert' => true,
    'username'      => $username,
    'password'      => $password,
    'protocol'      => 'imap'
]);

$client->connect();
$inbox = $client->getFolder('INBOX');
$message = $inbox->messages()->limit(1)->get()->first();

if ($message) {
    echo "Subject: " . $message->getSubject() . PHP_EOL;
} else {
    echo "No messages found." . PHP_EOL;
}

