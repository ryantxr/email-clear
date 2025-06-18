<?php

namespace App\Services;

use App\Models\UserToken;
use App\Lib\OpenAiModels;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\ClientManager as ImapClientManager;
use Webklex\PHPIMAP\Client as ImapClient;

class MailScanner
{
    protected int $maxMessages;
    protected int $throttleMs;

    public function __construct(
        protected HttpClient $httpClient = new HttpClient(),
        ?int $maxMessages = null,
        ?int $throttleMs = null,
    ) {
        $this->maxMessages = $maxMessages ?? (int) config('scanner.max_messages');
        $this->throttleMs = $throttleMs ?? (int) config('scanner.throttle_ms');
    }

    /**
     * Scan the inbox for solicitation emails.
     */
    public function scanGmail(UserToken $token, string $username, string $openaiKey, string $model = OpenAiModels::GPT_41_NANO): void
    {
        $accessToken = $this->refreshAccessToken($token);
        if (!$accessToken) {
            Log::warning('Skipping scan: invalid token for ID ' . $token->id);
            return;
        }

        $client = (new ImapClientManager())->make([
            'host'           => 'imap.gmail.com',
            'port'           => 993,
            'encryption'     => 'ssl',
            'validate_cert'  => true,
            'username'       => $username,
            'password'       => $accessToken,
            'authentication' => 'oauth',
        ]);

        $client->connect();
        $inbox = $client->getFolder('INBOX');

        $query = $inbox->messages()->since($token->last_scanned_at ?: Carbon::now()->subDays(7));
        if (method_exists($query, 'limit')) {
            $query->limit($this->maxMessages);
        }
        $messages = $query->get();

        $mostRecent = $token->last_scanned_at;
        $count = 0;
        foreach ($messages as $message) {
            if ($count >= $this->maxMessages) {
                break;
            }
            $date = $message->getDate();
            if (!$mostRecent || $date->gt($mostRecent)) {
                $mostRecent = $date;
            }
            $body = $message->getTextBody() ?: $message->getHTMLBody();
            $analysis = $this->classify($body, $openaiKey, $model);
            usleep($this->throttleMs * 1000);
            $count++;
            // TODO: add Gmail labeling via API
        }

        if ($mostRecent) {
            $token->last_scanned_at = $mostRecent;
            $token->save();
        }
    }

    /**
     * Scan a plain IMAP account using username and password.
     */
    public function scanImap(
        string $host,
        int $port,
        string $encryption,
        string $username,
        string $password,
        string $openaiKey,
        string $model = OpenAiModels::GPT_41_NANO
    ): void {
        $client = (new ImapClientManager())->make([
            'host'          => $host,
            'port'          => $port,
            'encryption'    => $encryption,
            'validate_cert' => true,
            'username'      => $username,
            'password'      => $password,
            'protocol'      => 'imap',
        ]);
        try {
            $client->connect();
        } catch (\Throwable $e) {
            Log::error('IMAP login failed: ' . $e->getMessage());
            return;
        }

        $this->processFolder($client, 'INBOX', $openaiKey, $model, "host: $host, username: $username");
        $this->processFolder($client, 'Junk', $openaiKey, $model, "host: $host, username: $username");
    }

    /**
     * 
     */
    protected function processFolder(ImapClient $client, string $folder, string $openaiKey, string $model, string $label)
    {
        $inbox = $client->getFolder($folder);
        //
        $query = $inbox->messages()->since(Carbon::now()->subDays(7));
        if (method_exists($query, 'limit')) {
            $query->limit($this->maxMessages);
        }
        $messages = $query->get();

        $messageCount = $messages->count();
        Log::channel('mailread')->info("{$folder} {$label} | {$messageCount} messages.");
        foreach ($messages as $message) {
            $body = $message->getTextBody() ?: $message->getHTMLBody();
            $date = $message->getDate();
            $subject = $message->getSubject();
            $fromAttr = $message->getFrom();
            $from = '';
            if (is_iterable($fromAttr)) {
                $parts = [];
                foreach ($fromAttr as $addr) {
                    $parts[] = (string) $addr;
                }
                $from = implode(', ', $parts);
            } else {
                $from = (string) $fromAttr;
            }
            // TODO: leave this commented for now
            // We will enable this at a later time
            if ( config('services.emailclear.enable_ai') ) {
                $jsonResponse = $this->classify($body, $openaiKey, $model);
            } else {
                $jsonResponse = $this->classifyFake($body, $openaiKey, $model);
            }
            Log::channel('mailread')->info("{$jsonResponse}");

            $responseObject = json_decode($jsonResponse);
            $score = $responseObject->short + $responseObject->pitch + $responseObject->request_call + $responseObject->optout;
            $timestamp = $date instanceof Carbon ? $date->toIso8601String() : (string) $date;
            Log::channel('mailread')->info("{$timestamp} | From: {$from} | Subject: {$subject} | Score: {$score}");
            

            usleep($this->throttleMs * 1000);
        }
    }

    /**
     * This is a stub to test this out to make sure everything is flowing correctly.
     */
    protected function classifyFake(string $body, string $key, string $model): string
    {
        return '{"short": 1,"pitch":1,"request_call":1, "optout":1}';
    }

    protected function classify(string $body, string $key, string $model): string
    {
        $prompt = "Given the following email, rate it 0 or 1 for each criterion:\n" .
            "1. Is the email short?\n" .
            "2. Is it pitching a product or service?\n" .
            "3. Is it requesting a short follow-up call (10-15 minutes)?\n" .
            "4. Does it contain opt-out language?\n" .
            "Provide each score, and then the total as 'Total'. in JSON format {\"short\": 1,\"pitch\":1,\"request_call\":1, \"optout\":1}\n" .
            "Email:\n{$body}";

        $data = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0,
        ];

        $response = $this->httpClient->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $key,
            ],
            'json' => $data,
        ]);

        $result = json_decode($response->getBody(), true);
        return trim($result['choices'][0]['message']['content'] ?? '');
    }

    protected function refreshAccessToken(UserToken $token): ?string
    {
        $tokenData = $token->token;

        // Bail out if token column does not contain JSON
        if (!is_array($tokenData)) {
            Log::warning('Token column is not JSON for token ID ' . $token->id);
            return null;
        }

        // If access token is still valid, return it
        $expiry = ($tokenData['created'] ?? 0) + ($tokenData['expires_in'] ?? 0) - 60;
        if (time() < $expiry && !empty($tokenData['access_token'])) {
            return $tokenData['access_token'];
        }

        $client = new \Google_Client();
        $client->setAuthConfig(config('services.google.credentials'));
        $client->setAccessType('offline');
        $client->refreshToken($token->refresh_token);
        $accessToken = $client->getAccessToken();
        $tokenData['access_token'] = $accessToken['access_token'] ?? null;
        $tokenData['expires_in'] = $accessToken['expires_in'] ?? ($tokenData['expires_in'] ?? 3600);
        $tokenData['created'] = time();
        $token->token = $tokenData;
        $token->save();

        return $tokenData['access_token'];
    }
}
