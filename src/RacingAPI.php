<?php

namespace RacingDataClient;

use Dotenv\Dotenv;
use RacingDataClient\Config\Config;
use RacingDataClient\Exceptions\EnvironmentFileNotFoundException;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use RacingDataClient\Contracts\HttpClientInterface;
use RacingDataClient\Utilities\Helpers;

class RacingAPI extends Container
{
    /**
     * Base path.
     *
     * @var array
     */
    protected string $basePath;

    /**
     * The manager config.
     *
     * @var \RacingDataClient\Config\Config
     */
    protected Config $config;

    /**
     * Create a new DataAPI Instance.
     *
     * @param  array  $paths  The paths to be used for the config.
     *
     * @throws \RacingDataClient\Exceptions\EnvironmentFileNotFoundException
     */
    public function __construct(protected array $paths = [])
    {
        // Create a new config instance and set the base path.
        $this->config = new Config();
        $this->basePath = dirname(__FILE__, 5);

        // If the environment file does not exist, throw exception.
        if (! $paths && ! file_exists($this->basePath.'/.env')) {
            throw new EnvironmentFileNotFoundException('Environment file is missing at '.$this->basePath.'/.env');
        }

        // Merge passed paths with default.
        $this->paths = array_merge([
            'env_file_path' => $this->basePath,
            'env_file' => $this->basePath.'/.env',
            'config_path' => $this->basePath.'/config',
        ], $paths);

        // Register the default bindings.
        $this->registerBaseBindings();

        // Make collections recursive.
        Collection::macro('recursive', function ($collection) {
            return $this->map(function ($value) {
                if (is_array($value) || is_object($value)) {
                    $subCollection = new Collection($value);
                    $subCollection->recursive();
                }

                return $value;
            });
        });
    }

    /**
     * Magic method to call appropriate methods on the manager instance.
     *
     * @param  string  $method  The method name.
     * @param  array  $arguments  The parameters for the appropriate method.
     * @return mixed
     */
    public function __call(string $method, $arguments): mixed
    {
        return call_user_func_array([$this->getManagerInstance(), $method], $arguments);
    }

    /**
     * Magic method to call appropriate methods statically on the manager instance.
     *
     * @param  string  $method  The method name.
     * @param  array  $arguments  The parameters for the appropriate method.
     * @return mixed
     */
    public static function __callStatic(string $method, $arguments): mixed
    {
        $self = new self;

        return call_user_func_array([$self->getManagerInstance(), $method], $arguments);
    }

    /**
     * Register the default container bindings.
     *
     * @return void
     */
    private function registerBaseBindings(): void
    {
        // Load the configuration.
        $this->config->loadConfigurationFiles($this->paths['config_path'], $this->getEnvironment());

        // Set the default instance on the container.
        static::setInstance($this);

        // Create the config instance.
        $this->instance('config', $this->config);

        // Create the manager singleton.
        $this->singleton(RacingAPIManager::class, function ($app) {
            return new RacingAPIManager($app);
        });

        // Create the helpers singleton.
        $this->singleton(Helpers::class, function ($app) {
            return new Helpers;
        });

        // Bind the HTTP client interface to the container.
        $this->bind(HttpClientInterface::class, 'RacingDataClient\\HttpClients\\Guzzle');
    }

    /**
     * Get the current environment from the .env file.
     *
     * @return string
     */
    private function getEnvironment(): string
    {
        // Check to see if a config file exists.
        if (is_file($this->paths['env_file'])) {
            // If so, create a new immutable Dotenv instance.
            $dotenv = Dotenv::createImmutable($this->basePath);

            // Load the environment config.
            $dotenv->load($this->paths['env_file_path']);
        }

        // Else, return production if no environment variable is found.
        return getenv('ENVIRONMENT') ?: 'production';
    }

    /**
     * Get a manager instance.
     *
     * @return \RacingDataClient\RacingAPIManager
     */
    private function getManagerInstance(): RacingAPIManager
    {
        // Return the manager instance.
        return $this->make(RacingAPIManager::class);
    }
}
