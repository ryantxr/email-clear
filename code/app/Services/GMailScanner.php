<?php

namespace App\Services;

use App\Models\UserToken;
use App\Lib\OpenAiModels;
use App\Services\MailClassifier;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Label;
use Google_Service_Gmail_ModifyMessageRequest;

class GMailScanner
{
    protected int $maxMessages;
    protected int $throttleMs;
    protected MailClassifier $classifier;

    public function __construct(
        protected HttpClient $httpClient = new HttpClient(),
        ?int $maxMessages = null,
        ?int $throttleMs = null,
    ) {
        $this->maxMessages = $maxMessages ?? (int) config('scanner.max_messages');
        $this->throttleMs = $throttleMs ?? (int) config('scanner.throttle_ms');
        $this->classifier = new MailClassifier($this->httpClient); 
    }

    /**
     * Scan the inbox for solicitation emails.
     */
    public function scanGmail(UserToken $token, string $username, string $openaiKey, string $model = OpenAiModels::GPT_41_NANO): ?int
    {
        $accessToken = $this->refreshAccessToken($token);
        if (!$accessToken) {
            Log::warning('Skipping scan: invalid token for ID ' . $token->id);
            return null;
        }

        $googleClient = new Google_Client();
        $googleClient->setAuthConfig(config('services.google.credentials'));
        $googleClient->setAccessToken(['access_token' => $accessToken]);

        $service = new Google_Service_Gmail($googleClient);

        $solicitationLabelId = $this->solicitationLabelId($service);

        $after = $token->last_scanned_at
            ? 'after:' . $token->last_scanned_at->timestamp
            : 'newer_than:7d';
        $list = $service->users_messages->listUsersMessages('me', [
            'q' => $after,
            'maxResults' => $this->maxMessages,
        ]);
        $messages = $list->getMessages() ?? [];

        $mostRecent = $token->last_scanned_at;
        $count = 0;
        foreach ($messages as $ref) {
            if ($count >= $this->maxMessages) {
                Log::channel('mailread')->warning("Exceeded max messages");
                break;
            }
            $message = $service->users_messages->get('me', $ref->getId(), ['format' => 'full']);
            $internalDate = (int) $message->getInternalDate();
            $date = Carbon::createFromTimestampMs($internalDate);
            if (!$mostRecent || $date->gt($mostRecent)) {
                $mostRecent = $date;
            }

            $payload = $message->getPayload();
            $bodyData = $payload->getBody()->getData();
            if (!$bodyData && $payload->getParts()) {
                foreach ($payload->getParts() as $part) {
                    if ($part->getMimeType() === 'text/plain' && $part->getBody()) {
                        $bodyData = $part->getBody()->getData();
                        break;
                    }
                }
            }
            $body = $bodyData ? base64_decode(strtr($bodyData, '-_', '+/')) : '';
            if (!$body) {
                $body = $message->getSnippet();
            }
            $from = '';
            $subject = '';
            foreach ($payload->getHeaders() as $header) {
                $name = strtolower($header->getName());
                if ($name === 'from') {
                    $from = $header->getValue();
                } elseif ($name === 'subject') {
                    $subject = $header->getValue();
                }
            }
            
            $jsonResponse = $this->classifier->classify($body, $openaiKey, $model);
            $responseObject = json_decode($jsonResponse);
            $score = $responseObject->short + $responseObject->pitch + $responseObject->request_call + $responseObject->optout;

            $timestamp = $date instanceof Carbon ? $date->toIso8601String() : (string) $date;
            Log::channel('mailread')->info("{$timestamp} | From: {$from} | Subject: {$subject} | Score: {$score}");
            Log::channel('mailread')->debug($jsonResponse);
            // Right here, we will label the email if it isn't already labeled.
            if ($responseObject->pitch && $responseObject->request_call && $responseObject->optout) {
                $existing = $message->getLabelIds();
                if (!in_array($solicitationLabelId, $existing ?? [], true)) {
                    Log::channel('mailread')->info("Add solicitation label");
                    $mods = new Google_Service_Gmail_ModifyMessageRequest();
                    $mods->setAddLabelIds([$solicitationLabelId]);
                    $service->users_messages->modify('me', $message->getId(), $mods);
                    $count++;
                }
            }
            usleep($this->throttleMs * 1000);
        }

        if ($mostRecent) {
            $token->last_scanned_at = $mostRecent;
            $token->save();
        }
        return $count;
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

    /**
     * Retrieve the ID for the solicitation label, creating it if necessary.
     */
    protected function solicitationLabelId(Google_Service_Gmail $service): string
    {
        $labelName = 'solicitation';
        $list = $service->users_labels->listUsersLabels('me');
        foreach ($list->getLabels() as $label) {
            if (strtolower($label->getName()) === $labelName) {
                return $label->getId();
            }
        }

        $label = new Google_Service_Gmail_Label();
        $label->setName($labelName);
        $label->setLabelListVisibility('labelShow');
        $label->setMessageListVisibility('show');

        $created = $service->users_labels->create('me', $label);

        return $created->getId();
    }
}
