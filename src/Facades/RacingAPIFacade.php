<?php

namespace RacingData\Facades;

use Illuminate\Support\Facades\Facade;
use RacingData\Contracts\ClientInterface;

/**
 * @method static \RacingData\Contracts\ClientInterface driver(string $driver = null)
 *
*/
class RacingAPIFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ClientInterface::class;
    }
}
