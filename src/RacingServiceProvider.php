<?php

namespace RacingPackage;

use Illuminate\Support\ServiceProvider;

class RacingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
    */
    public function boot(): void
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
    */
    public function register(): void
    {
        $this->app->singleton(RacingAPI::class, function ($app) {
            return new RacingAPI($app);
        });
    }
}