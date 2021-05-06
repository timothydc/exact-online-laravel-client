<?php

return [

    /**
     * The Client ID of your Exact Online application.
     */
    'client_id' => env('EXACT_CLIENT_ID', ''),

    /**
     * The Client Secret of your Exact Online application.
     */
    'client_secret' => env('EXACT_CLIENT_SECRET', ''),

    /**
     * The Webhook Secret of your Exact Online application.
     */
    'client_webhook_secret' => env('EXACT_CLIENT_WEBHOOK_SECRET', ''),

    'redirect_url' => env('EXACT_REDIRECT_URL', ''),

    /**
     * The Division number of your Exact Online Division.
     */
    'division' => env('EXACT_DIVISION', ''),

    /**
     * The Base URL for the Exact Online Connection.
     */
    'base_url' => env('EXACT_BASE_URL', 'https://start.exactonline.be'),

    /**
     * The Language used in language specific description fields.
     */
    'language_code' => env('EXACT_LANGUAGE_CODE', ''),

    'routing' => [
        'prefix' => env('EXACT_ROUTE_PREFIX', 'exact-online'),
        'middleware' => env('EXACT_ROUTE_MIDDLEWARE', ['web', 'auth']),
    ],

    'token_storage' => [
        'use_filesystem' => env('EXACT_USE_FILESYSTEM_STORAGE', false),
        'filesystem' => [
            /*
             * Set a disk from your config/filesystems.php
             */
            'disk' => env('EXACT_TOKEN_DISK', ''),
            /*
             * Path to the token storage file
             */
            'path' => env('EXACT_TOKEN_PATH', 'token_credentials_' . env('EXACT_CLIENT_ID', '') . '.json'),
        ],
    ],
];