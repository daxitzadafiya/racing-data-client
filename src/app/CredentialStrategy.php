<?php

namespace RacingPackage\app;

interface CredentialStrategy
{
    public function setCredentials(): array;
}