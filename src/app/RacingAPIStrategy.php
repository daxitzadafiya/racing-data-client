<?php

namespace RacingPackage\app;

use Illuminate\Support\Facades\Config;

class RacingAPIStrategy implements CredentialStrategy
{
    protected $config;

    public function __construct()
    {
        $this->config = Config::get('racing.default');
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