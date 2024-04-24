<?php

namespace RacingDataClient\Facades;

use Illuminate\Support\Facades\Facade;
use RacingDataClient\Contracts\ClientInterface;

/**
 * @method static \RacingDataClient\Contracts\ClientInterface driver(string $driver = null)
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
