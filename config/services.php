<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'quickbooks' => [
        'client_id' => env('QB_CLIENT_ID', 'ABm9qvtavSMWRyCJkXVY1AT8jHT40pamIKga5zCVa3T2xHO8o5'),
        'client_secret' => env('QB_CLIENT_SECRET', 'tfyECfprb0vu0WRa3KiP0PNJP119htIpj6HAfxxJ'),
        'redirect' => env('QB_REDIRECT_URI', 'http://localhost:9000/app/integrations/quickbooks/callback'),
        'sandbox' => env('QB_SANDBOX', true),
        'minor_version' => env('QB_MINOR_VERSION', '75'),
        'scopes' => 'com.intuit.quickbooks.accounting openid profile email',
    ],

    'gmail' => [
        'client_id' => env('GMAIL_CLIENT_ID'),
        'client_secret' => env('GMAIL_CLIENT_SECRET'),
        'redirect' => env('GMAIL_REDIRECT_URI'),
        'scopes' => 'https://www.googleapis.com/auth/gmail.readonly',
    ],

    'xero' => [
        'client_id' => env('XERO_CLIENT_ID', 'B65AB64D962B4A1AAEA656B0E825AA4F'),
        'client_secret' => env('XERO_CLIENT_SECRET', 'TIpPCYZOjCMG2ld_7N4nI_TsmVjoiT-tkVfNKcxEi7TaOY96'),
        'redirect' => env('XERO_REDIRECT_URI', 'http://localhost:9000/app/integrations/xero/callback'),
        'scopes' => 'openid profile email accounting.contacts.read offline_access',
    ],

    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'price_id' => env('STRIPE_PRICE_ID'),
    ],

];
