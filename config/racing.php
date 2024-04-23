<?php

return [
    // 'default' => env('RACING_API_DRIVER', 'racing'),
    'default' => env('RACING_API_DRIVER'),
    'racing' => [
        'driver' => 'racing',
        // "base_url" => env('RACING_API_BASE_URL', 'https://api.theracingapi.com/v1/'),
        "base_url" => env('RACING_API_BASE_URL'),
        'credentials' => [
            // "username" => env('RACING_API_USERNAME', 'GLbMje7eIZPuloW6h1VrQqyD'),
            "username" => env('RACING_API_USERNAME'),
            // "password" => env('RACING_API_PASSWORD', 'f36A4je27Lit4oIUtzBrDkU7')
            "password" => env('RACING_API_PASSWORD')
        ]
    ]
];
