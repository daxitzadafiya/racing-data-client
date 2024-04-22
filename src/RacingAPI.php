<?php 
 
namespace RacingPackage;

use Illuminate\Container\Container;
use RacingPackage\Config\Config;
use Dotenv\Dotenv;
use RacingPackage\Exceptions\EnvironmentFileNotFoundException;

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
     * @var \RacingPackage\Config\Config
    */
    protected Config $config;

    /**
     * Create a new DataAPI Instance.
     *
     * @param  array  $paths The paths to be used for the config.
     *
     * @throws \RacingPackage\Exceptions\EnvironmentFileNotFoundException
    */
    public function __construct(protected array $paths = [])
    {
        // Create a new config instance and set the base path.
        $this->config = new Config();
        $this->basePath = dirname(getcwd(), 1);

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
}