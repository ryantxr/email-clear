<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GoogleConfigServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $path = config('services.google.credentials');

        $credentials = [];

        if (!env('GOOGLE_CLIENT_ID') || !env('GOOGLE_CLIENT_SECRET') || !env('GOOGLE_REDIRECT_URI')) {
            if ($path && is_file($path)) {
                $json = json_decode(file_get_contents($path), true);

                if (isset($json['web'])) {
                    $json = $json['web'];
                } elseif (isset($json['installed'])) {
                    $json = $json['installed'];
                }

                $credentials = $json ?? [];
            }
        }

        $defaultRedirect = rtrim(config('app.url'), '/') . '/settings/gmail/callback';

        config([
            'services.google.client_id' => env('GOOGLE_CLIENT_ID', $credentials['client_id'] ?? null),
            'services.google.client_secret' => env('GOOGLE_CLIENT_SECRET', $credentials['client_secret'] ?? null),
            'services.google.redirect' => env('GOOGLE_REDIRECT_URI', $credentials['redirect_uris'][0] ?? $credentials['redirect_uri'] ?? $defaultRedirect),
        ]);
    }
}
