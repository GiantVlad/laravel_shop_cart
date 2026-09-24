<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        // The dashboard is opened by the app's admin guard (the same session as /admin), not by
        // $request->user(), which resolves the default `web` guard - i.e. a *customer* account.
        // Leave HORIZON_ALLOWED_EMAILS (config/horizon.php) empty to let any admin in, or set it
        // to a comma separated list to narrow it down.
        Gate::define('viewHorizon', function ($user = null) {
            $admin = Auth::guard('admin')->user();

            if ($admin === null) {
                return false;
            }

            $allowlist = config('horizon.allowed_emails', []);

            return $allowlist === [] || in_array($admin->email, $allowlist, true);
        });
    }
}
