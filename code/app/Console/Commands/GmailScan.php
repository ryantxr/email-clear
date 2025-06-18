<?php

namespace App\Console\Commands;

use App\Models\UserToken;
use App\Services\MailScanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Lib\OpenAiModels;

class GmailScan extends Command
{
    protected $signature = 'gmail:scan';

    protected $description = 'Scan Gmail accounts for solicitation emails';

    public function handle(MailScanner $scanner): int
    {
        $openai = config('services.openai.key');
        $model = config('services.openai.model', OpenAiModels::GPT_41_NANO);
        foreach (UserToken::all() as $token) {
            $user = $token->user;
            if (method_exists($user, 'canScanMore') && !$user->canScanMore()) {
                Log::info('monthly limit reached for user ' . $user->id);
                continue;
            }
            try {
                $count = $scanner->scanGmail($token, $token->email, $openai, $model);
                if (method_exists($user, 'incrementMonthlyScanned')) {
                    $user->incrementMonthlyScanned($count);
                }
            } catch (\Throwable $e) {
                Log::error('scan failed: ' . $e->getMessage());
            }
        }

        return self::SUCCESS;
    }
}
