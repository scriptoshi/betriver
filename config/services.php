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

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
    ],
    'payeer' => [
        'shop' => env('PAYEER_SHOP'),
        'merchant_key' => env('PAYEER_MERCHANT_KEY'),
        'accountNumber' => env('PAYEER_ACCOUNT_NUMBER'),
        'apiId' => env('PAYEER_APIID'),
        'apiSecret' => env('PAYEER_API_SECRET'),
    ],

    'nowpayments' => [
        'api_key' => env('NOWPAYMENTS_APIKEY'),
        'api_secret' => env('NOWPAYMENTS_IPN_SECRET'),
    ],

    'coinpayments' => [
        'public_key' => env('COINPAYMENTS_PUBLIC_KEY'),
        'private_key' => env('COINPAYMENTS_PRIVATE_KEY'),
        'merchant_id' => env('COINPAYMENTS_MERCHANTID'),
        'ipn_secret' => env('COINPAYMENTS_IPN_SECRET'),
    ],

    'smspoh' => [
        'endpoint' => 'https://smspoh.com/api/v2/send',
        'token' => env('SMSPOH_TOKEN'),
        'sender' =>  env('SMSPOH_SENDER')
    ],

    'touchsms' => [
        'token_id' => env('TOUCHSMS_TOKEN_ID'),
        'access_token' => env('TOUCHSMS_ACCESS_TOKEN'),
        'default_sender' => env('TOUCHSMS_DEFAULT_SENDER'),
    ],

    'clickatell' => [
        'apiKey' => env('CLICKATELL_APIKEY')
    ],


    'messagebird' => [
        'access_key' => env('MESSAGEBIRD_ACCESS_KEY'),
        'originator' => env('MESSAGEBIRD_ORIGINATOR'),
    ],

    'vonage' => [
        'api_key' => env('VONAGE_API_KEY'),
        'api_secret' => env('VONAGE_API_SECRET'),
        'sms_sender' => env('VONAGE_SMS_SENDER'),
    ],

    'textmagic' => [
        'apiv2_key' => env('TEXTMAGIC_APIV2_KEY'),
        'username' => env('TEXTMAGIC_USERNAME'),
    ],


    'coincap' => [
        'apikey' => env('COINCAP_APIKEY', null)
    ],

    'apifootball' => [
        'apikey' => env('APIFOOTBALL_APIKEY', null)
    ],

    'mailgun' => [
        'secret' => env('MAILGUN_SECRET'),
        'domain' =>  env('MAILGUN_DOMAIN'),
    ],
    'sendgrid' => [
        'key' => env('SENDGRID_KEY'),
    ],
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'mailjet' => [
        'key' => env('MAILJET_APIKEY'),
        'secret' => env('MAILJET_APISECRET'),
    ],
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'project_id' => env('GOOGLE_PROJECT_ID'),
        'project_id' => env('GOOGLE_PROJECT_ID'),
        'redirect' => env('GOOGLE_CLIENT_REDIRECT'),
    ],
    'github' => [
        'client_id' =>  env('GITHUB_CLIENT_ID'),
        'client_secret' =>  env('GITHUB_CLIENT_SECRET'),
        'redirect' =>  env('GITHUB_REDIRECT'),
    ],
    'facebook' => [
        'client_id' =>  env('FACEBOOK_CLIENT_ID'),
        'client_secret' =>  env('FACEBOOK_CLIENT_SECRET'),
        'redirect' =>  env('FACEBOOK_REDIRECT'),
    ],
];
