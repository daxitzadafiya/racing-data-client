<?php

namespace RacingData\Clients;

use Exception;
use RacingData\Contracts\ClientInterface;
use RacingData\Contracts\HttpClientInterface;
use RacingData\Exceptions\BadRequestException;
use RacingData\Exceptions\ClientException;
use RacingData\Exceptions\MethodNotAllowedException;
use RacingData\Exceptions\ResourceNotFoundException;
use RacingData\Exceptions\TooManyRequestsException;
use RacingData\Exceptions\UnauthorisedRequestException;
use Illuminate\Support\Collection;
use RacingData\Utilities\Helpers;

abstract class Client implements ClientInterface
{
    /**
     * Create a client instance.
     *
     * @param  array  $config  THe client configuration.
     * @param  \RacingData\Contracts\HttpClientInterface  $client  The HTTP client.
     */
    public function __construct(protected array $config, protected HttpClientInterface $client, protected Helpers $helpers)
    {
    }

    /**
     * Perform a HTTP request.
     *
     * @param  string  $method  HTTP method GET, POST, PUT/PATCH, DELETE
     * @param  string|array  $resource  Resource endpoint.
     * @param  array  $options  Request options.
     * @return \Illuminate\Support\Collection
     *
     * @throws \RacingData\Exceptions\BadRequestException
     * @throws \RacingData\Exceptions\ClientException
     * @throws \RacingData\Exceptions\MethodNotAllowedException
     * @throws \RacingData\Exceptions\TooManyRequestsException
     * @throws \RacingData\Exceptions\UnauthorisedRequestException
     */
    public function request(string $method, string|array $resource, array $options = []): Collection
    {
        try {
            // Build the request URL and perform the request.
            $response = $this->client->{$method}($this->buildUrl($resource), $options);

            // Return the response collection.
            return collect(json_decode($response->getBody()->getContents(), true));
        } catch (Exception $e) {
            return $this->throwException($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Build the request URL.
     *
     * @param  string|array  $resource  The API resource.
     * @return string
     */
    public function buildUrl(string|array $resource): string
    {
        // Default request URL.
        return $this->config['base_url'].$resource;
    }

    /**
     * Throw a custom exception based on error code.
     *
     * @param  int  $code  The exception error code.
     * @param  string  $message  The exception message.
     *
     * @throws \RacingData\Exceptions\BadRequestException
     * @throws \RacingData\Exceptions\ClientException
     * @throws \RacingData\Exceptions\MethodNotAllowedException
     * @throws \RacingData\Exceptions\TooManyRequestsException
     * @throws \RacingData\Exceptions\UnauthorisedRequestException
     * @throws \RacingData\Exceptions\ResourceNotFoundException
     */
    public function throwException(int $code, string $message)
    {
        switch ($code) {
            case 503:
                throw new TooManyRequestsException($message, $code);
            case 405:
                throw new MethodNotAllowedException($message, $code);
            case 404:
                throw new ResourceNotFoundException($message, $code);
            case 403:
                throw new UnauthorisedRequestException($message, $code);
            case 400:
                throw new BadRequestException($message, $code);
            default:
                throw new ClientException($message, $code);
        }
    }
}
