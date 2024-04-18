<?php

namespace RacingPackage\facades;

use Illuminate\Support\Facades\Facade;

class CredentialsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'configuration';
    }
}