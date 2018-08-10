<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'influx' => [
        'host' => env('INFLUX_HOST', ''),
        'port' => env('INFLUX_POST', ''),
        'username' => env('INFLUX_USERNAME', ''),
        'password' => env('INFLUX_PASSWORD', ''),
        'database' => env('INFLUX_DATABASE', 'analytics')
    ],

    'elasticsearch' => [
        'host' => env('ELASTICSEARCH_HOST', '127.0.0.1'),
        'port' => env('ELASTICSEARCH_PORT', '9200'),
    ]

];
