<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'docs/*'],

    'allowed_methods' => ['*'],

    /*
    | The Flutter/Android app calls the API directly (not subject to browser
    | CORS), so this only matters for browser-based callers: the Scribe docs
    | "try it out" page and any admin/SPA frontend. Default to APP_URL only;
    | add more via a comma-separated CORS_ALLOWED_ORIGINS env value.
    */
    'allowed_origins' => array_values(array_filter(array_map(
        fn ($origin) => rtrim(trim($origin), '/'),
        explode(',', env('CORS_ALLOWED_ORIGINS', env('APP_URL', '')))
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
