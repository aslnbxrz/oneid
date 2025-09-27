<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OneID Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for OneID API endpoints. This is the official OneID
    | authentication service URL for Uzbekistan.
    |
    */
    'base_url' => env('ONEID_BASE_URL', 'https://sso.egov.uz'),

    /*
    |--------------------------------------------------------------------------
    | OneID Client Credentials
    |--------------------------------------------------------------------------
    |
    | Your OneID application credentials. These are provided when you
    | register your application with OneID system.
    |
    */
    'client_id' => env('ONEID_CLIENT_ID'),
    'client_secret' => env('ONEID_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | OneID Scope
    |--------------------------------------------------------------------------
    |
    | The scope parameter defines what permissions your application
    | is requesting from OneID.
    |
    */
    'scope' => env('ONEID_SCOPE', 'openid profile'),

    /*
    |--------------------------------------------------------------------------
    | Redirect URI
    |--------------------------------------------------------------------------
    |
    | The URI where OneID will redirect after successful authentication.
    | This must match the redirect URI configured in your OneID application.
    |
    */
    'redirect_uri' => env('ONEID_REDIRECT_URI'),

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    |
    | OneID API endpoints configuration.
    |
    */
    'endpoints' => [
        'authorization' => env('ONEID_AUTH_ENDPOINT', '/sso/oauth/Authorization.do'),
        'token' => env('ONEID_TOKEN_ENDPOINT', '/sso/oauth/Authorization.do'),
        'user_info' => env('ONEID_USER_INFO_ENDPOINT', '/sso/oauth/Authorization.do'),
        'logout' => env('ONEID_LOGOUT_ENDPOINT', '/sso/oauth/Authorization.do'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OneID package routes. You can disable routes entirely
    | by setting 'enabled' to false and use only facade methods.
    |
    */
    'routes' => [
        'enabled' => env('ONEID_ROUTES_ENABLED', true),
        'prefix' => env('ONEID_ROUTE_PREFIX', 'auth/oneid'),
        'middleware' => env('ONEID_ROUTE_MIDDLEWARE', 'web'),
        'names' => [
            'handle' => 'oneid.handle',
            'logout' => 'oneid.logout',
            'redirect' => 'oneid.redirect',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Data Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for handling user data from OneID response.
    |
    */
    'user' => [
        'pin_field' => env('ONEID_PIN_FIELD', 'pin'),
        'required_fields' => ['pin', 'first_name', 'sur_name', 'mid_name'],
        'optional_fields' => [
            'valid', 'validation_method', 'user_id', 'full_name', 'pport_no',
            'birth_date', 'sur_name', 'user_type', 'sess_id', 'ret_cd',
            'auth_method', 'pkcs_legal_tin', 'legal_info',
        ],
        // Rasmiy hujjatdagi field mapping
        'field_mapping' => [
            'pin' => 'pin',
            'first_name' => 'first_name',
            'last_name' => 'sur_name',
            'middle_name' => 'mid_name',
            'full_name' => 'full_name',
            'birth_date' => 'birth_date',
            'passport' => 'pport_no',
            'user_type' => 'user_type',
            'session_id' => 'sess_id',
            'auth_method' => 'auth_method',
            'is_verified' => 'valid',
            'validation_methods' => 'validation_method',
            'legal_entities' => 'legal_info',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for HTTP client used to communicate with OneID API.
    |
    */
    'http' => [
        'timeout' => env('ONEID_TIMEOUT', 30),
        'connect_timeout' => env('ONEID_CONNECT_TIMEOUT', 10),
        'retry_times' => env('ONEID_RETRY_TIMES', 3),
        'retry_delay' => env('ONEID_RETRY_DELAY', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Headers
    |--------------------------------------------------------------------------
    |
    | Default headers to be sent with every request to OneID API.
    |
    */
    'default_headers' => [
        'Accept' => 'application/json',
        'User-Agent' => 'Laravel-OneID-Package/1.0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for logging OneID API requests and responses.
    |
    */
    'logging' => [
        'enabled' => env('ONEID_LOGGING_ENABLED', true),
        'level' => env('ONEID_LOG_LEVEL', 'info'),
        'channel' => env('ONEID_LOG_CHANNEL', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for caching OneID responses.
    |
    */
    'cache' => [
        'enabled' => env('ONEID_CACHE_ENABLED', false),
        'ttl' => env('ONEID_CACHE_TTL', 3600), // 1 hour
        'prefix' => env('ONEID_CACHE_PREFIX', 'oneid'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security-related configuration for OneID integration.
    |
    */
    'security' => [
        'verify_ssl' => env('ONEID_VERIFY_SSL', true),
        'allowed_origins' => env('ONEID_ALLOWED_ORIGINS', '*'),
        'rate_limiting' => [
            'enabled' => env('ONEID_RATE_LIMITING_ENABLED', true),
            'max_attempts' => env('ONEID_RATE_LIMIT_ATTEMPTS', 5),
            'decay_minutes' => env('ONEID_RATE_LIMIT_DECAY', 1),
        ],
    ],
];
