<?php

namespace RacingPackage\lib;

use RacingPackage\traits\ClientTrait;

require_once __DIR__ . '/../config/constant.php';

class TheRacingAPI
{
    use ClientTrait;

    protected $base_url;

    public function __construct()
    {
        $this->base_url = BASE_URL;
    }
}
