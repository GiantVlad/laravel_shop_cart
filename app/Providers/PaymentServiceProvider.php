<?php

namespace App\Providers;

use App\Library\Services\IpspPaymentService;
use App\Library\Services\Ipsp\Api as IspsApi;
use Illuminate\Support\ServiceProvider;
use App\Library\Services\PaymentServiceInterface;

class PaymentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->bind(PaymentServiceInterface::class, fn () => new IpspPaymentService(new IspsApi()));
    }
}
