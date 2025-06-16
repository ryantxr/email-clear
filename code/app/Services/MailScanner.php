<?php

namespace App\Services;

use App\Models\UserToken;
use App\Models\ImapAccount;
use App\Lib\OpenAiModels;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use Webklex\PHPIMAP\ClientManager;

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
     * Scan a Gmail inbox for solicitation emails.
     */
    public function scanGmailAccount(UserToken $token, string $username, string $openaiKey, string $model = OpenAiModels::GPT_41_NANO): void
    {
        $accessToken = $this->refreshAccessToken($token);

        $client = (new ClientManager())->make([
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

        $query = $inbox->messages()->since($token->last_scanned_at ?: Carbon::now()->subDays(2));
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
     * Scan a standard IMAP account.
     */
    public function scanImapAccount(ImapAccount $account, string $openaiKey, string $model = OpenAiModels::GPT_41_NANO): void
    {
        $client = (new ClientManager())->make([
            'host'          => $account->host,
            'port'          => $account->port,
            'encryption'    => $account->encryption,
            'validate_cert' => true,
            'username'      => $account->username,
            'password'      => $account->password,
        ]);

        $client->connect();
        $inbox = $client->getFolder('INBOX');

        $query = $inbox->messages()->since(Carbon::now()->subDays(2));
        if (method_exists($query, 'limit')) {
            $query->limit($this->maxMessages);
        }
        $messages = $query->get();

        foreach ($messages as $message) {
            $body = $message->getTextBody() ?: $message->getHTMLBody();
            $this->classify($body, $openaiKey, $model);
            usleep($this->throttleMs * 1000);
        }
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

    protected function refreshAccessToken(UserToken $token): string
    {
        $client = new \Google_Client();
        $client->setAuthConfig(config('services.google.credentials'));
        $client->setAccessType('offline');
        $client->refreshToken($token->refresh_token);
        $accessToken = $client->getAccessToken();
        return $accessToken['access_token'];
    }
}
