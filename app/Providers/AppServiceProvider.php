<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Catalog;
use App\Product;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $catalogs = Catalog::where('parent_id', NULL)->get();

        View::share('catalogs', $catalogs);

        //Delete record from "product_property" table
        Product::deleting(function ($product) {
            $product->properties()->sync([]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
