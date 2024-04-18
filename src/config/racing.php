<?php

return [
    'default' => getenv('RACING_API_DEFAULT'),
    'racing' => [
        'driver' => 'racing',
        "base_url" => getenv('RACING_API_BASE_URL'),
        'credentials' => [
            "username" => getenv('RACING_API_USERNAME'),
            "password" => getenv('RACING_API_PASSWORD')
        ]
    ]
];
