<?php

namespace RacingData\Contracts;

interface Factory
{
    /**
     * Get a data provider implementation.
     *
     * @param  null|string  $driver
     * @return \RacingData\Contracts\ClientInterface
     */
    public function with(null|string $driver = null);
}
