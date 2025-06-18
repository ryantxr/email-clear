<?php
// Simple OAuth callback handler
$config = include __DIR__ . '/config.php';

$postUrl = $config['post_to'] ?? null;
$redirectUrl = $config['redirect_to'] ?? '/';

$data = [];
if (isset($_GET['code'])) {
    $data['code'] = $_GET['code'];
}
if (isset($_GET['state'])) {
    $data['state'] = $_GET['state'];
}

if ($postUrl) {
    $ch = curl_init($postUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

header('Location: ' . $redirectUrl, true, 303);
exit;


