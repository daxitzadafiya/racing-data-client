<?php

namespace RacingPackage\providers;

use Illuminate\Support\ServiceProvider;
use RacingPackage\app\RacingAPIStrategy;

class RacingServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('configuration', function () {
            return new RacingAPIStrategy();
        });
    }
}