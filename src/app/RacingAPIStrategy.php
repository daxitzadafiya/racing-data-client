<?php

namespace RacingPackage\app;

class RacingAPIStrategy implements CredentialStrategy
{
    public function setCredentials(): array
    {
        $config = require_once __DIR__ . "/../config/racing.php";

        return [
            "base_url" => $config['configurations'][$config['default']]['base_url'],
            "auth" => [
                $config['configurations'][$config['default']]['credentials']['username'],
                $config['configurations'][$config['default']]['credentials']['password']
            ]
        ];
    }
}