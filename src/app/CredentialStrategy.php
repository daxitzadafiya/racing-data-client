<?php

namespace App;

interface CredentialStrategy
{
    public function setCredentials($base_url, $credentials): array;
}