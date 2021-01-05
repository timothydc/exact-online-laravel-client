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
    'language_code' => env('EXACT_LANGUAGE_CODE', 'NL-BE'),

    /**
     * This option controls the method which is used to store the OAuth token.
     *
     * Supported: "database", "file"
     */
    'token_storage_method' => env('EXACT_TOKEN_STORAGE', 'database'),
];