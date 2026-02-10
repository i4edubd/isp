<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default SMS Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default SMS provider that will be used by the
    | framework when no provider is explicitly specified.
    |
    */

    'default' => env('SMS_PROVIDER', 'maestro'),

    /*
    |--------------------------------------------------------------------------
    | SMS Provider Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the SMS providers used by your application
    | as well as their respective settings. Several examples have been
    | provided for you.
    |
    */

    'providers' => [

        'maestro' => [
            'driver' => 'maestro',
            'api_key' => env('MAESTRO_SMS_API_KEY'),
            'sender_id' => env('MAESTRO_SMS_SENDER_ID'),
            'url' => 'https://example.com/maestro/api/send', // Placeholder URL
        ],

        'robi' => [
            'driver' => 'robi',
            'api_key' => env('ROBI_SMS_API_KEY'),
            'sender_id' => env('ROBI_SMS_SENDER_ID'),
            'url' => 'https://example.com/robi/api/send',
        ],
        
        // Add other providers from TODO.md here...
        // 'm2m' => [ ... ],
        // 'bdbangladesh' => [ ... ],
        // ...

        'log' => [
            'driver' => 'log',
            'channel' => env('SMS_LOG_CHANNEL', 'sms'),
        ],

    ],

];
