<?php

namespace App\Console\Commands;

use App\Models\ImapAccount;
use App\Services\MailScanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImapScan extends Command
{
    protected $signature = 'imap:scan';

    protected $description = 'Scan standard IMAP accounts for solicitation emails';

    public function handle(MailScanner $scanner): int
    {
        $openai = config('services.openai.key');
        $model = config('services.openai.model', 'gpt-3.5-turbo');

        foreach (ImapAccount::all() as $account) {
            try {
                $scanner->scanImapAccount($account, $openai, $model);
            } catch (\Throwable $e) {
                Log::error('imap scan failed: ' . $e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
