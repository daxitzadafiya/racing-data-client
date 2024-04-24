<?php

namespace RacingData\HttpClients;

use RacingData\Contracts\HttpClientInterface;
use GuzzleHttp\Client;

class Guzzle extends Client implements HttpClientInterface
{
}
