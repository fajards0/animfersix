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

    'otakudesu' => [
        'base_url' => env('OTAKUDESU_API_BASE_URL', 'http://127.0.0.1:3000/api'),
        'source_url' => env('OTAKUDESU_SOURCE_URL', 'https://otakudesu.best'),
        'disable_proxy' => env('OTAKUDESU_DISABLE_PROXY', true),
        'proxy_images' => env('OTAKUDESU_PROXY_IMAGES', env('APP_ENV') !== 'local'),
        'warm_pages' => max(1, (int) env('OTAKUDESU_WARM_PAGES', env('APP_ENV') === 'local' ? 1 : 2)),
        'warm_genres' => array_values(array_filter(array_map('trim', explode(',', (string) env(
            'OTAKUDESU_WARM_GENRES',
            env('APP_ENV') === 'local'
                ? 'action,fantasy'
                : 'action,fantasy,romance,comedy,school,adventure'
        ))))),
    ],

];
