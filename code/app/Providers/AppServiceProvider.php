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
        $this->app->bind(GMailScanner::class, function ($app) {
            return new GMailScanner(
                new HttpClient(),
                $app['config']->get('scanner.max_messages'),
                $app['config']->get('scanner.throttle_ms')
            );
        });
        $this->app->bind(ImapMailScanner::class, function ($app) {
            return new ImapMailScanner(
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
