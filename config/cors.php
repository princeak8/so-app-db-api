<?php

return [
    'paths' => ['*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [ '*',
        'http://localhost:5173',           // Development
        'http://localhost:5174',           // Development
        'http://localhost:5176',
    ],

     'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
        'Accept',
        'Origin',
        'X-CSRF-TOKEN'
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true
];