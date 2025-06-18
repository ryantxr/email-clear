<?php

namespace App\Services;

use App\Models\UserToken;
use App\Lib\OpenAiModels;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Log;

class MailClassifier
{

    public function __construct(
        protected HttpClient $httpClient = new HttpClient())
    {
    }

    /**
     * This is a stub to test this out to make sure everything is flowing correctly.
     */
    public function classifyFake(string $body, string $key, string $model): string
    {
        return '{"short": 1,"pitch":1,"request_call":1, "optout":1}';
    }

    public function classify(string $body, string $key, string $model): string
    {
        $prompt = "Given the following email, rate it 0 or 1 for each criterion:\n" .
            "1. Is the email short?\n" .
            "2. Is it pitching a product or service?\n" .
            "3. Is it requesting a short follow-up call (10-15 minutes)?\n" .
            "4. Does it contain opt-out language?\n" .
            "Provide each score. in JSON format {\"short\": 1,\"pitch\":1,\"request_call\":1, \"optout\":1}\n" .
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
}