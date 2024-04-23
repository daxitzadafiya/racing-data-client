<?php

namespace RacingPackage\HttpClients;

use RacingPackage\Contracts\HttpClientInterface;
use GuzzleHttp\Client;

class Guzzle extends Client implements HttpClientInterface
{
}
