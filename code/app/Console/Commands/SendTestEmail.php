<?php
// app/Console/Commands/SendTestEmail.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    protected $signature = 'mail:test {to}';
    protected $description = 'Send a test email';

    public function handle()
    {
        $to = $this->argument('to');

        Mail::raw('This is a test email from your Laravel application.', function ($message) use ($to) {
            $message->to($to)
                    ->subject('Test Email');
        });

        $this->info("Test email sent to {$to}");
    }
}
