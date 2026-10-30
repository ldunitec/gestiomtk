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

       'todoist' => [
       'url' => env('TODOIST_URL', 'https://api.todoist.com/api/v1/'),
       'token' => env('TODOIST_TOKEN'),
       'project_id' => env('TODOIST_PROJECT_ID'),
       'users' => [
           'ld' => env('TODOIST_LD'),
           'cl' => env('TODOIST_CL'),
           'hm' => env('TODOIST_HM'),
           'kg' => env('TODOIST_KG'),
       ],
   
    ],
    
    'anviz' => [
    'ip'   => env('ANVIZ_IP'),
    'user' => env('ANVIZ_USER'),
    // 'pass' => env('ANVIZ_PASS'),
],



];
