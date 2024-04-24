<?php

namespace RacingData;

use RacingData\Contracts\Factory;
use RacingData\Contracts\HttpClientInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use RacingData\Utilities\Helpers;

class RacingAPIServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/racing.php' => $this->app->configPath('racing.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        // Retrieve the configuration.
        $config = $this->app->make('config')->get('racing');

        if ($config) {
            $this->app->singleton(Factory::class, function ($app) {
                return new RacingAPIManager($app);
            });

            $this->app->bind(HttpClientInterface::class, 'RacingData\\HttpClients\\Guzzle');

            $this->app->singleton(Helpers::class, function ($app) {
                return new Helpers;
            });
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Factory::class,
            HttpClientInterface::class,
            Helpers::class
        ];
    }
}
