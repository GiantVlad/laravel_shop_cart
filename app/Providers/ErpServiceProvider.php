<?php

namespace App\Providers;

use App\Services\Erp\ErpServiceInterface;
use App\Services\Erp\ErpServiceStub;
use App\Services\Warehouse\WarehouseServiceInterface;
use App\Services\Warehouse\WarehouseServiceStub;
use App\Temporal\ErpActivity;
use App\Temporal\ErpActivityInterface;
use App\Temporal\WarehouseActivity;
use App\Temporal\WarehouseActivityInterface;
use App\Temporal\OrderCompletedWorkflow;
use App\Temporal\OrderCompletedWorkflowInterface;
use Illuminate\Support\ServiceProvider;

class ErpServiceProvider extends ServiceProvider
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
        $this->app->singleton(OrderCompletedWorkflowInterface::class, OrderCompletedWorkflow::class);
        $this->app->singleton(WarehouseActivityInterface::class, WarehouseActivity::class);
        $this->app->singleton(WarehouseServiceInterface::class, WarehouseServiceStub::class);
        $this->app->singleton(ErpActivityInterface::class, ErpActivity::class);
        $this->app->singleton(ErpServiceInterface::class, ErpServiceStub::class);
    }
}
