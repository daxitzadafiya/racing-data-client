<?php

namespace RacingPackage\app;

class RacingAPIStrategy implements CredentialStrategy
{
    protected $config;

    public function __construct()
    {
        $this->config = require_once __DIR__ . "/../config/racing.php";
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