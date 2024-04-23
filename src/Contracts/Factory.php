<?php

namespace RacingPackage\Contracts;

interface Factory
{
    /**
     * Get a data provider implementation.
     *
     * @param  null|string  $driver
     * @return \RacingPackage\Contracts\ClientInterface
     */
    public function with(null|string $driver = null);
}
