<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Product;
use App\Catalog;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot ()
    {
        if (!$this->app->runningInConsole()) {
            $catalog = new Catalog;
            View::share('catalogs', $catalog->parentsNode());
        }

        //Delete records from "product_property" table
        Product::deleting(function ($product) {
            $product->properties()->detach();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register ()
    {
        //
    }
}
