<?php

namespace App\Providers;

use App\Services\MailScanner;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MailScanner::class, function ($app) {
            return new MailScanner(
                new HttpClient(),
                $app['config']->get('scanner.max_messages'),
                $app['config']->get('scanner.throttle_ms')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
