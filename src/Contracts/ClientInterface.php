<?php

namespace RacingDataClient\Contracts;

use Exception;
use Illuminate\Support\Collection;

interface ClientInterface
{
    /**
     * Perform a HTTP request.
     *
     * @param  string  $method  HTTP method GET, POST, PUT/PATCH, DELETE
     * @param  string  $resource  Resource endpoint.
     * @param  array  $options  Request options.
     * @return \Illuminate\Support\Collection
     *
     * @throws \Exception
     */
    public function request(string $method, string $resource, array $options = []): Collection;

    /**
     * Build the API URL.
     *
     * @param  string  $resource  The resource endpoint.
     * @return string
     */
    public function buildUrl(string $resource): string;

    /**
     * Throw a custom exception based on error code.
     *
     * @param  int  $code  The exception error code.
     * @param  string  $message  The exception message.
     *
     * @throws \RacingDataClient\Exceptions\BadRequestException
     * @throws \RacingDataClient\Exceptions\ClientException
     * @throws \RacingDataClient\Exceptions\MethodNotAllowedException
     * @throws \RacingDataClient\Exceptions\TooManyRequestsException
     * @throws \RacingDataClient\Exceptions\UnauthorisedRequestException
     */
    public function throwException(int $code, string $message);
}
