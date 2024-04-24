<?php

namespace RacingDataClient\Contracts;

interface HttpClientInterface
{
    /**
     * Perform a GET HTTP request.
     *
     * @param  string  $url  Resource endpoint.
     * @param  array  $parameters  Request options.
     * @return object
     */
    public function get(string $url, array $parameters = []): object;

    /**
     * Perform a POST HTTP request.
     *
     * @param  string  $url  Resource endpoint.
     * @param  array  $parameters  Request options.
     * @return object
     */
    public function post(string $url, array $parameters = []): object;

    /**
     * Perform a PUT HTTP request.
     *
     * @param  string  $url  Resource endpoint.
     * @param  array  $parameters  Request options.
     * @return object
     */
    public function put(string $url, array $parameters = []): object;

    /**
     * Perform a PATCH HTTP request.
     *
     * @param  string  $url  Resource endpoint.
     * @param  array  $parameters  Request options.
     * @return object
     */
    public function patch(string $url, array $parameters = []): object;

    /**
     * Perform a DELETE HTTP request.
     *
     * @param  string  $url  Resource endpoint.
     * @param  array  $parameters  Request options.
     * @return object
     */
    public function delete(string $url, array $parameters = []): object;
}
