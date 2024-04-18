<?php

namespace RacingPackage\app;

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