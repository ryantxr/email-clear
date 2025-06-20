<?php
/*
This file is only for doing oauth on local.
In a case where the application is running on vm and it lives on a
made up domain, this file can be run on localhost and post the values
back to the app via a local-only api call.
*/
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
Log::debug(json_encode(print_r($_GET, true)));
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
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // allow self-signed certs
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $result = curl_exec($ch);
    // Check for error and log it
    if ($result === false) {
        $err = curl_error($ch);
        $errno = curl_errno($ch);
        Log::debug("cURL error ($errno): $err");
    } else {
        Log::debug($result);
    }
    curl_close($ch);
}

header('Location: ' . $redirectUrl, true, 303);
exit;


