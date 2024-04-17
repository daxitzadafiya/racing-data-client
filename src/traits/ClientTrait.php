<?php

namespace RacingPackage\traits;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Exception\GuzzleException;

require_once __DIR__ . '/../config/constant.php';

trait ClientTrait 
{
    public function fetchDataFromAPI($url, $method)
    {
        $client = new Client();

        try {
            $response = $client->request($method, $url, [
                'auth' => [API_USERNAME, API_PASSWORD]
            ]);
    
            // Check if the response status code is 200 (OK)
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody(), true);
            }
    
            // Return some error message or code if not 200
            return "Error: " . $response->getStatusCode();
    
        } catch (GuzzleException $e) {
            // Handle the exception
            return "Request failed: " . $e->getMessage();
        }
    }

    public function fetchMeetingData($url, $method) {
        $callback = [];

        $client = new Client();

        // Initiate asynchronous requests
        $promiseRaces = $client->requestAsync('GET', $url[0], $callback);
        $promiseForm = $client->requestAsync('GET', $url[1], $callback);
        $promiseRunners = $client->requestAsync('GET', $url[2], $callback);

        // Wait for all promises to complete and get the responses
        $responses = Promise\unwrap([$promiseRaces, $promiseForm, $promiseRunners]);

        // Decode JSON from each response
        $races = json_decode($responses[0]->getBody(), true);
        $form = json_decode($responses[1]->getBody(), true);
        $runners = json_decode($responses[2]->getBody(), true);

        // Package into a keyed array
        $resultArray = [
            'races' => $races,
            'forms' => $form,
            'runners' => $runners,
        ];

        return $resultArray;
    }
}