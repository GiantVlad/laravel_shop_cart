<?php

namespace App\Providers;

use App\Services\Warehouse\WarehouseServiceInterface;
use App\Services\Warehouse\WarehouseServiceStab;
use App\Temporal\WarehouseActivity;
use App\Temporal\WarehouseActivityInterface;
use App\Temporal\WarehouseWorkflow;
use App\Temporal\WarehouseWorkflowInterface;
use Illuminate\Support\ServiceProvider;

class WarehouseServiceProvider extends ServiceProvider
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
        $this->app->singleton(WarehouseWorkflowInterface::class, WarehouseWorkflow::class);
        $this->app->singleton(WarehouseActivityInterface::class, WarehouseActivity::class);
        $this->app->singleton(WarehouseServiceInterface::class, WarehouseServiceStab::class);
    }
}
