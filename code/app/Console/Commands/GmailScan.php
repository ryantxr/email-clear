<?php

namespace App\Console\Commands;

use App\Models\UserToken;
use App\Services\MailScanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GmailScan extends Command
{
    protected $signature = 'gmail:scan';

    protected $description = 'Scan Gmail accounts for solicitation emails';

    public function handle(MailScanner $scanner): int
    {
        $openai = config('services.openai.key');
        $model = config('services.openai.model', 'gpt-3.5-turbo');
        foreach (UserToken::all() as $token) {
            try {
                $scanner->scan($token, $token->email, $openai, $model);
            } catch (\Throwable $e) {
                Log::error('scan failed: ' . $e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
