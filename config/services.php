<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'github' => [
        'client_id' => env('AUTH_GITHUB_CLIENT_ID', ''),
        'client_secret' => env('AUTH_GITHUB_CLIENT_SECRET', ''),
        'redirect' => '/auth/github/callback',
    ],
    'google' => [
        'client_id' => env('AUTH_GOOGLE_CLIENT_ID', ''),
        'client_secret' => env('AUTH_GOOGLE_CLIENT_SECRET', ''),
        'redirect' => '/auth/google/callback',
    ],
    'microsoft' => [
        'client_id' => env('AUTH_MICROSOFT_CLIENT_ID', ''),
        'client_secret' => env('AUTH_MICROSOFT_CLIENT_SECRET', ''),
        'redirect' => '/auth/microsoft/callback',
        'tenant' => 'common',
    ],
    'uipath' => [
        'client_id' => env('AUTH_UIPATH_CLIENT_ID', ''),
        'client_secret' => '',
        'redirect' => '/auth/uipath/callback',
    ],
];
