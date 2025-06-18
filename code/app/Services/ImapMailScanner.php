<?php

namespace App\Services;

use App\Models\UserToken;
use App\Lib\OpenAiModels;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\ClientManager as ImapClientManager;
use Webklex\PHPIMAP\Client as ImapClient;

class ImapMailScanner
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
    ): ?int {
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
            return null;
        }

        $total = 0;
        $total += $this->processFolder($client, 'INBOX', $openaiKey, $model, "host: $host, username: $username");
        $total += $this->processFolder($client, 'Junk', $openaiKey, $model, "host: $host, username: $username");
        return $total;
    }

    /**
     * 
     */
    protected function processFolder(ImapClient $client, string $folder, string $openaiKey, string $model, string $label): int
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
        $count = 0;
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
                $jsonResponse = $this->classifier->classify($body, $openaiKey, $model);
            } else {
                $jsonResponse = $this->classifier->classifyFake($body, $openaiKey, $model);
            }
            Log::channel('mailread')->info("{$jsonResponse}");

            $responseObject = json_decode($jsonResponse);
            $score = $responseObject->short + $responseObject->pitch + $responseObject->request_call + $responseObject->optout;
            $timestamp = $date instanceof Carbon ? $date->toIso8601String() : (string) $date;
            Log::channel('mailread')->info("{$timestamp} | From: {$from} | Subject: {$subject} | Score: {$score}");


            usleep($this->throttleMs * 1000);
            $count++;
        }
        return $count;
    }
}
