<?php

namespace App\Services;

use App\Models\UserToken;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use Webklex\PHPIMAP\ClientManager;

class MailScanner
{
    public function __construct(
        protected HttpClient $httpClient = new HttpClient()
    ) {
    }

    /**
     * Scan the inbox for solicitation emails.
     */
    public function scan(UserToken $token, string $username, string $openaiKey, string $model = 'gpt-3.5-turbo'): void
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
        $messages = $query->get();

        $mostRecent = $token->last_scanned_at;
        foreach ($messages as $message) {
            $date = $message->getDate();
            if (!$mostRecent || $date->gt($mostRecent)) {
                $mostRecent = $date;
            }

            $body = $message->getTextBody() ?: $message->getHTMLBody();
            $analysis = $this->classify($body, $openaiKey, $model);
            // TODO: add Gmail labeling via API
        }

        if ($mostRecent) {
            $token->last_scanned_at = $mostRecent;
            $token->save();
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
