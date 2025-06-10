<?php
require 'vendor/autoload.php';

use App\MailScanner;
use Dotenv\Dotenv;

$host = 'imap.gmail.com';
$port = '993';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$username = $_ENV['USERNAME'] ?? null;
$password = $_ENV['PASSWORD'] ?? null;
$openai = $_ENV['OPENAI_API_KEY'] ?? null;
$model  = $_ENV['OPENAI_MODEL'] ?? 'gpt-3.5-turbo';
$lastFile = __DIR__.'/../docs/last_scan.json';

$scanner = new MailScanner($host, $port, $username, $password, $openai, $lastFile, $model, function($m){ echo $m."\n"; });
$scanner->scan();

