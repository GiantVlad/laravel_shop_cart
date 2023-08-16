<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
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
    public function boot (): void
    {
        if(env('APP_HTTPS_FOR_STATIC', false))
        {
            try {
                $this->app->get('request')->server->set('HTTPS', 'on');
                URL::forceScheme('https');
            } catch (\Throwable $e) {
            }
            
        }
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
