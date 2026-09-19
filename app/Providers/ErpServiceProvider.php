<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Erp\ErpServiceInterface;
use App\Services\Erp\ErpServiceStub;
use App\Services\Warehouse\WarehouseServiceInterface;
use App\Services\Warehouse\WarehouseServiceStub;
use Illuminate\Support\ServiceProvider;

class ErpServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->singleton(WarehouseServiceInterface::class, WarehouseServiceStub::class);
        $this->app->singleton(ErpServiceInterface::class, ErpServiceStub::class);
    }
}
