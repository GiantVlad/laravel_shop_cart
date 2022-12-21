<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowClientInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot ()
    {
//        if (!$this->app->runningInConsole()) {
//            $catalog = new Catalog;
//            View::share('catalogs', $catalog->parentsNode());
//        }
//
//        //Delete records from "product_property" table
//        Product::deleting(function ($product) {
//            $product->properties()->detach();
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register ()
    {
        $this->app->singleton(
            WorkflowClientInterface::class,
            fn () => WorkflowClient::create(
                ServiceClient::create('temporal:7233')
            )
        );
    }
}
