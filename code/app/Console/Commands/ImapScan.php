<?php
namespace App\Console\Commands;

use App\Services\ImapMailScanner as MailScanner;
use App\Models\ImapAccount;
use Illuminate\Console\Command;
use App\Lib\OpenAiModels;
use Illuminate\Support\Facades\Log;
class ImapScan extends Command
{
    protected $signature = 'imap:scan';

    protected $description = 'Scan stored IMAP accounts for solicitation emails';

    public function handle(MailScanner $scanner): int
    {
        $openai = config('services.openai.key');
        $model = config('services.openai.model', OpenAiModels::GPT_41_NANO);

        foreach (ImapAccount::all() as $account) {
            $user = $account->user;
            if (method_exists($user, 'canScanMore') && !$user->canScanMore()) {
                Log::info('monthly limit reached for user ' . $user->id);
                continue;
            }
            Log::info($account->host);
            $count = $scanner->scanImap(
                $account->host,
                $account->port,
                $account->encryption ?? 'ssl',
                $account->username,
                $account->password,
                $openai,
                $model
            );
            if (method_exists($user, 'incrementMonthlyScanned')) {
                $user->incrementMonthlyScanned($count);
            }
        }
        return self::SUCCESS;
    }
}
