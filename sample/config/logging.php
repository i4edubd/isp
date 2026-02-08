<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'customer_disconnect' => [
            'driver' => 'daily',
            'path' => storage_path('logs/customer_disconnect.log'),
            'level' => 'debug',
            'days' => 3,
        ],

        'debug' => [
            'driver' => 'daily',
            'path' => storage_path('logs/debug.log'),
            'level' => 'debug',
            'days' => 3,
        ],

        'validate_mobile' => [
            'driver' => 'daily',
            'path' => storage_path('logs/validate_mobile.log'),
            'level' => 'debug',
            'days' => 3,
        ],

        'sql_relay' => [
            'driver' => 'daily',
            'path' => storage_path('logs/sql_relay.log'),
            'level' => 'debug',
            'days' => 2,
        ],

        'sql_relay_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/sql_relay_error.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 7,
        ],

        'hotspot_active_login' =>  [
            'driver' => 'daily',
            'path' => storage_path('logs/hotspot_active_login.log'),
            'level' => 'debug',
            'days' => 3,
        ],

        'stale_sessions' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stale_sessions.log'),
            'level' => 'debug',
            'days' => 3,
        ],

        'restart_freeradius' => [
            'driver' => 'daily',
            'path' => storage_path('logs/restart_freeradius.log'),
            'level' => 'debug',
            'days' => 7,
        ],

        'telegram_bot' => [
            'driver' => 'daily',
            'path' => storage_path('logs/telegram_bot.log'),
            'level' => 'debug',
            'days' => 3,
        ],

        'sms_bill' => [
            'driver' => 'daily',
            'path' => storage_path('logs/sms_bill.log'),
            'level' => 'debug',
            'days' => 3,
        ],

        'international_attributes' => [
            'driver' => 'daily',
            'path' => storage_path('logs/international_attributes.log'),
            'level' => 'debug',
            'days' => 15,
        ],

        'auto_suspend_customers' => [
            'driver' => 'daily',
            'path' => storage_path('logs/auto_suspend_customers.log'),
            'level' => 'debug',
            'days' => 3,
        ],

    ],

];
