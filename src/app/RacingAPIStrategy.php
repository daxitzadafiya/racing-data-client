<?php

namespace App;

class RacingAPIStrategy implements CredentialStrategy
{
    public function setCredentials($base_url, $credentials): array
    {
        return [
            "base_url" => $base_url,
            "auth" => [
                $credentials['username'],
                $credentials['password']
            ]
        ];
    }
}