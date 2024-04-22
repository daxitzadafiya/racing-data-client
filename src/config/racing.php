<?php

return [
    'default' => env('RACING_API_DRIVER', 'racing'),
    'racing' => [
        'driver' => 'racing',
        "base_url" => env('RACING_API_BASE_URL', 'https://api.theracingapi.com/v1/'),
        'credentials' => [
            "username" => env('RACING_API_USERNAME', 'GLbMje7eIZPuloW6h1VrQqyD'),
            "password" => env('RACING_API_PASSWORD', 'f36A4je27Lit4oIUtzBrDkU7')
        ]
    ]
];
