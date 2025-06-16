<?php
namespace App\Console\Commands;

use App\Services\MailScanner;
use App\Models\ImapAccount;
use Illuminate\Console\Command;

class ImapScan extends Command
{
    protected $signature = 'imap:scan';

    protected $description = 'Scan stored IMAP accounts for solicitation emails';

    public function handle(MailScanner $scanner): int
    {
        $openai = config('services.openai.key');
        $model = config('services.openai.model', 'gpt-3.5-turbo');

        foreach (ImapAccount::all() as $account) {
            $scanner->scanImap(
                $account->host,
                $account->port,
                $account->encryption ?? 'ssl',
                $account->username,
                $account->password,
                $openai,
                $model
            );
        }
        return self::SUCCESS;
    }
}
