<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    */
    'default' => env('PAYMENT_GATEWAY', 'sslcommerz'),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configurations
    |--------------------------------------------------------------------------
    */
    'providers' => [

        'sslcommerz' => [
            'driver' => 'sslcommerz',
            'store_id' => env('SSLCOMMERZ_STORE_ID'),
            'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
            'sandbox' => env('SSLCOMMERZ_SANDBOX', true),
        ],

        'bkash' => [
            'driver' => 'bkash',
            'app_key' => env('BKASH_APP_KEY'),
            'app_secret' => env('BKASH_APP_SECRET'),
            'username' => env('BKASH_USERNAME'),
            'password' => env('BKASH_PASSWORD'),
            'sandbox' => env('BKASH_SANDBOX', true),
        ],

        'nagad' => [
            'driver' => 'nagad',
            // ... nagad specific credentials
        ],
        
        'recharge_card' => [
            'driver' => 'recharge_card',
        ],

        // ... other gateways from TODO.md
    ],

];
