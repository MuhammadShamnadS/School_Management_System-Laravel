<?php
// config/cors.php

/*
|--------------------------------------------------------------------------
| Laravel CORS
|--------------------------------------------------------------------------
|
| This file is where you may configure your settings for cross-origin
| resource sharing (CORS). You can adjust these settings as needed.
|
*/

use Illuminate\Support\Facades\Route;       

return [
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'broadcasting/auth', // 👈 allow this
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173', // 👈 Vite dev
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
