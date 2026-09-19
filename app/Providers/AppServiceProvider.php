<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (env('APP_HTTPS_FOR_STATIC', false)) {
            try {
                $this->app->get('request')->server->set('HTTPS', 'on');
                URL::forceScheme('https');
            } catch (\Throwable $e) {
            }
        }
    }

    public function register(): void
    {
        // Temporal SDK reference removed; sync controllers + Horizon used instead
    }
}
