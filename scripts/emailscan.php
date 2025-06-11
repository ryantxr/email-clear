<?php
require 'vendor/autoload.php';

use App\MailScanner;
use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$host = 'imap.gmail.com';
$port = '993';

// Setup Monolog logger
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}
$logger = new Logger('emailscan');
$logger->pushHandler(new StreamHandler($logDir . '/emailscan.log', Logger::INFO));

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$email = $_ENV['USERNAME'] ?? null;
$password = $_ENV['PASSWORD'] ?? null;
$openai = $_ENV['OPENAI_API_KEY'] ?? null;
$model  = $_ENV['OPENAI_MODEL'] ?? 'gpt-3.5-turbo';
$lastFile = __DIR__.'/../docs/last_scan.json';

$scanner = new MailScanner(
    $host,
    $port,
    $email,
    $password,
    $openai,
    $lastFile,
    $model,
    function ($m) use ($logger) {
        $logger->info($m);
    }
);
$scanner->scan();

