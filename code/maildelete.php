<?php
require 'vendor/autoload.php';
use App\MailDeleter;
// IMAP server details
$host = 'imap.gmail.com';
$port = '993';
$username = GET FROM .env
$password = GET FROM .env

try {

    $deleter = new MailDeleter($host, $port, $username, $password, function($message){
        echo $message . "\n";
    });
    
    $deleter->delete();
} catch ( \Exception $e ) {
    echo $e->getMessage() . "\n";
}

echo "Process completed.\n";

