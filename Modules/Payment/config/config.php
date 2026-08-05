<?php

return [
    'name' => 'Payment',

    'mpesa' => [
        'env' => env('MPESA_ENV', 'sandbox'),
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
        'passkey' => env('MPESA_PASSKEY'),
        'initiator_name' => env('MPESA_INITIATOR_NAME'),
        'initiator_password' => env('MPESA_INITIATOR_PASSWORD'),
        'callback_url' => env('MPESA_CALLBACK_URL'),
        'callback_secret' => env('MPESA_CALLBACK_SECRET'),
    ],

    'bank' => [
        'name' => env('BANK_NAME', 'Equity Bank Kenya'),
        'account_name' => env('BANK_ACCOUNT_NAME', 'Kazilink Digital Academy Ltd'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', '0123456789'),
        'branch' => env('BANK_BRANCH', 'Westlands, Nairobi'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
];
