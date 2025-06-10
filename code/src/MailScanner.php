<?php
namespace App;

use Webklex\PHPIMAP\ClientManager;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;

class MailScanner
{
    protected $host;
    protected $port;
    protected $username;
    protected $password;
    protected $openaiKey;
    protected $out;
    protected $lastScanFile;
    protected $model;

    public function __construct($host, $port, $username, $password, $openaiKey, $lastScanFile, $model = 'gpt-3.5-turbo', ?\Closure $out = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->openaiKey = $openaiKey;
        $this->lastScanFile = $lastScanFile;
        $this->model = $model;
        $this->out = $out;
    }

    protected function loadLastScan()
    {
        if (file_exists($this->lastScanFile)) {
            $json = json_decode(file_get_contents($this->lastScanFile), true);
            return !empty($json['last_scan']) ? Carbon::parse($json['last_scan']) : null;
        }
        return null;
    }

    protected function saveLastScan(Carbon $time)
    {
        file_put_contents($this->lastScanFile, json_encode(['last_scan' => $time->toIso8601String()]));
    }

    protected function out($message)
    {
        if ($this->out instanceof \Closure) {
            ($this->out)($message);
        }
    }

    protected function classify($body)
    {
        $prompt = "Given the following email, rate it 0 or 1 for each criterion:\n".
            "1. Is the email short?\n".
            "2. Is it pitching a product or service?\n".
            "3. Is it requesting a short follow-up call (10-15 minutes)?\n".
            "4. Does it contain opt-out language?\n".
            "Provide each score, and then the total as 'Total'. in JSON format {\"short\": 1,\"pitch\":1,\"request_call\":1, \"optout\":1}\n".
            "Email:\n{$body}";

        $data = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0
        ];

        $client = new HttpClient();
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $this->openaiKey,
            ],
            'json' => $data,
        ]);

        $result = json_decode($response->getBody(), true);
        $content = $result['choices'][0]['message']['content'] ?? '';
        return trim($content);
    }

    public function scan()
    {
        $lastScan = $this->loadLastScan();
        $client = (new ClientManager())->make([
            'host'          => $this->host,
            'port'          => $this->port,
            'encryption'    => 'ssl',
            'validate_cert' => true,
            'username'      => $this->username,
            'password'      => $this->password,
            'protocol'      => 'imap'
        ]);
        $client->connect();
        $inbox = $client->getFolder('INBOX');
        $query = $inbox->messages()->unseen();
        if ($lastScan) {
            $query = $query->since($lastScan);
        }
        $messages = $query->get();
        $mostRecent = $lastScan;
        foreach ($messages as $message) {
            $date = $message->getDate();
            if (!$mostRecent || $date->gt($mostRecent)) {
                $mostRecent = $date;
            }
            $body = $message->getTextBody() ?: $message->getHTMLBody();
            $analysis = $this->classify($body);
            $this->out($analysis);
        }
        if ($mostRecent) {
            $this->saveLastScan($mostRecent);
        }
    }
}
