<?php

namespace RacingDataClient;

use RacingDataClient\Clients\Client;
use RacingDataClient\Contracts\Factory;
use RacingDataClient\Contracts\HttpClientInterface;
use RacingDataClient\Exceptions\DriverNotConfiguredException;
use Illuminate\Support\Manager;
use RacingDataClient\Clients\RacingAPIClient;
use RacingDataClient\Exceptions\HttpClientNotFoundException;
use RacingDataClient\Utilities\Helpers;

class RacingAPIManager extends Manager implements Factory
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     *
     * @deprecated Will be removed in a future Socialite release.
     */
    protected $app;

    /**
     * Get a data provider implementation.
     *
     * @param  null|string  $driver
     * @return \RacingDataClient\Contracts\ClientInterface
     */
    public function with(null|string $driver = null)
    {
        return $this->driver($driver);
    }

    /**
     * Create a RacingAPIClient instance.
     *
     * @return \RacingDataClient\Clients\RacingAPIClient
     */
    public function createRacingDriver(): RacingAPIClient
    {
        $this->ensureHttpClientIsInstalled();

        return $this->buildProvider(RacingAPIClient::class, $this->getConfig('racing'));
    }

    /**
     * Build a provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return \RacingDataClient\Clients\Client
     */
    public function buildProvider(string $provider, array $config): Client
    {
        return new $provider($config, $this->container->make(HttpClientInterface::class), $this->container->make(Helpers::class));
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        // Check that the driver configuration has a driver name set.
        if (is_null($this->container['config']['racing.driver'])) {
            // If not, return the null driver name.
            return 'null';
        }

        // Return the driver name from the config.
        return $this->container['config']['racing.driver'];
    }

    /**
     * Get the client config.
     *
     * @return array
     *
     * @throws \RacingDataClient\Exceptions\DriverNotConfiguredException
     */
    private function getConfig(string|null $driver = null): array
    {
        $driver = ! is_null($driver) ? $driver : $this->getDefaultDriver();

        try {
            return $this->container['config']['racing.clients'][$driver];
        } catch (\ErrorException $e) {
            throw new DriverNotConfiguredException('The passed driver, '.$driver.', is not configured.');
        }
    }

    /**
     * Ensure HTTP client is installed.
     *
     * @return void
     *
     * @throws \RacingDataClient\Exceptions\HttpClientNotFoundException
     */
    protected function ensureHttpClientIsInstalled(): void
    {
        // The HTTP clients namespace.
        $namespace = 'RacingDataClient\\HttpClients\\';

        // The HTTP client from the config.
        $client = 'Guzzle';

        // If the client exists, return.
        if (class_exists($namespace.$client)) {
            return;
        }

        // If not, throw an exception.
        throw new HttpClientNotFoundException('HTTP client '.$client.' not found.');
    }

    /**
     * Set the container instance used by the manager.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->app = $container;
        $this->container = $container;

        return $this;
    }
}
