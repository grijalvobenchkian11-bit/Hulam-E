<?php

return [

    'paths' => [
    'api/*',
    'auth/*',   // ← ADD THIS
    'sanctum/csrf-cookie',
],


    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:3001',
        'https://hulam-e-epho.onrender.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // IMPORTANT: must be false for API token auth
    'supports_credentials' => false,
];

