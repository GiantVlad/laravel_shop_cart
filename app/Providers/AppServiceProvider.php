<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // The app's single password policy. laravel/ui's ResetsPasswords (used by
        // both the customer and admin reset flows) validates with Password::defaults(),
        // so setting it here covers every screen where a user picks a password.
        Password::defaults(static fn () => Password::min(8)->letters()->numbers()->symbols());

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
