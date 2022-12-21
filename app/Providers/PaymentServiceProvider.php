<?php

namespace App\Providers;

use App\Library\Services\IpspPaymentService;
use App\Library\Services\Ipsp\Api as IspsApi;
use App\Temporal\PaymentActivity;
use App\Temporal\PaymentActivityInterface;
use App\Temporal\PaymentWorkflow;
use App\Temporal\PaymentWorkflowInterface;
use Illuminate\Support\ServiceProvider;
use App\Library\Services\PaymentServiceInterface;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PaymentServiceInterface::class, fn () => new IpspPaymentService(new IspsApi()));
        $this->app->singleton(PaymentActivityInterface::class, PaymentActivity::class);
        $this->app->singleton(PaymentWorkflowInterface::class, PaymentWorkflow::class);
    }
}
