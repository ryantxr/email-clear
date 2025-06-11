<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GmailScan::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // intentionally left empty, cron will trigger commands
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
