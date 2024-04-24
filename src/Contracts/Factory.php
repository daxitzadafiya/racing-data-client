<?php

namespace RacingDataClient\Contracts;

interface Factory
{
    /**
     * Get a data provider implementation.
     *
     * @param  null|string  $driver
     * @return \RacingDataClient\Contracts\ClientInterface
     */
    public function with(null|string $driver = null);
}
