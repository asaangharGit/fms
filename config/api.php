<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API client authentication
    |--------------------------------------------------------------------------
    |
    | Machine-to-machine clients (e.g. Power BI) authenticate with an API key
    | issued via `php artisan api-client:create`. Keys are stored as SHA-256
    | hashes only. The key is read from the "Authorization: Bearer" header or
    | the "X-Api-Key" header. Passing the key as a query parameter is disabled
    | by default because URLs end up in web-server access logs; enable it only
    | if you use the Power BI "Web API" credential type (ApiKeyName option).
    |
    */

    'auth' => [
        'allow_query_key' => env('API_ALLOW_QUERY_KEY', false),
        'query_key_name' => env('API_QUERY_KEY_NAME', 'api_key'),

        // Failed authentication attempts allowed per IP per minute.
        'max_failed_attempts' => (int) env('API_MAX_FAILED_ATTEMPTS', 10),

        // Minimum seconds between last_used_at writes for a client.
        'touch_interval' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Transport security
    |--------------------------------------------------------------------------
    |
    | Plain-HTTP requests are rejected unless the application runs in the
    | "local" or "testing" environment.
    |
    */

    'require_https' => env('API_REQUIRE_HTTPS', true),

    /*
    |--------------------------------------------------------------------------
    | Rate limiting & pagination
    |--------------------------------------------------------------------------
    */

    'rate_limit_per_minute' => (int) env('API_RATE_LIMIT_PER_MINUTE', 120),

    'pagination' => [
        'default_limit' => 1000,
        'max_limit' => 5000,
    ],

    /*
    |--------------------------------------------------------------------------
    | General Ledger API
    |--------------------------------------------------------------------------
    |
    | posted_statuses: voucher_status values treated as "posted" (default
    | filter). 3 = Approved, matching the trial balance / P&L / balance sheet
    | reports in ReportsController.
    |
    | max_report_days: largest date range accepted by the report endpoints.
    |
    */

    'general_ledger' => [
        'posted_statuses' => array_map('intval', array_filter(explode(',', env('GL_API_POSTED_STATUSES', '3')))),
        'max_report_days' => (int) env('GL_API_MAX_REPORT_DAYS', 3660),
    ],

];
