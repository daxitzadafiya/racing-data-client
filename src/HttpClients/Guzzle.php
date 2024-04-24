<?php

namespace RacingDataClient\HttpClients;

use RacingDataClient\Contracts\HttpClientInterface;
use GuzzleHttp\Client;

class Guzzle extends Client implements HttpClientInterface
{
}
