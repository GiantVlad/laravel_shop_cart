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
        defined('MERCHANT_ID') or define('MERCHANT_ID' , '1396424');
        defined('MERCHANT_PASSWORD') or define('MERCHANT_PASSWORD' , 'test');
        defined('IPSP_GATEWAY') or define('IPSP_GATEWAY' , 'api.fondy.eu');
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
