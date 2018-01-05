<?php

namespace Ipsp;

use Illuminate\Support\ServiceProvider;

class IpspServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        define('MERCHANT_ID' , '1396424');
        define('MERCHANT_PASSWORD' , 'test');
        define('IPSP_GATEWAY' , 'api.fondy.eu');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
