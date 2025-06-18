<?php
class Log
{
    static $file = __DIR__ . '/logs/output.log';
    public static function debug(string $s)
    {
        file_put_contents(self::$file, $s . PHP_EOL, FILE_APPEND);
    }
}

// Simple OAuth callback handler
$config = include __DIR__ . '/config.php';

$postUrl = $config['post_to'] ?? null;
$redirectUrl = $config['redirect_to'] ?? '/';

Log::debug("postUrl $postUrl");
Log::debug("redirectUrl $redirectUrl");

$data = [];
if (isset($_GET['code'])) {
    Log::debug("Code = " . $_GET['code']);
    $data['code'] = $_GET['code'];
}
if (isset($_GET['state'])) {
    Log::debug("State = " . $_GET['state']);
    $data['state'] = $_GET['state'];
}

if ($postUrl) {
    Log::debug("Posting data");
    $ch = curl_init($postUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    // Check for error and log it
    curl_close($ch);
}

header('Location: ' . $redirectUrl, true, 303);
exit;


