<?php

namespace RacingPackage\app;

interface CredentialStrategy
{
    public function setCredentials($base_url, $credentials): array;
}