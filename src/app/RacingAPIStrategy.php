<?php

namespace RacingPackage\app;

use Dotenv\Dotenv;
use RacingPackage\Exceptions\EnvironmentFileNotFoundException;

class RacingAPIStrategy implements CredentialStrategy
{
    protected $config;
    protected $basePath;

    public function __construct()
    {
        $this->basePath = getcwd();

        // If the environment file does not exist, throw exception.
        if (! file_exists($this->basePath.'/.env')) {
            throw new EnvironmentFileNotFoundException('Environment file is missing at '.$this->basePath.'/.env');
        }

        if (is_file($this->basePath.'/.env')) {
            // If so, create a new immutable Dotenv instance.
            $dotenv = Dotenv::createImmutable($this->basePath);

            // Load the environment config.
            $dotenv->load($this->basePath);
        }

        $this->config = require_once __DIR__ . "/../../config/racing.php";
    }

    public function setCredentials(): array
    {
        return [
            "base_url" => $this->config[$this->config['default']]['base_url'],
            "auth" => [
                $this->config[$this->config['default']]['credentials']['username'],
                $this->config[$this->config['default']]['credentials']['password']
            ]
        ];
    }
}