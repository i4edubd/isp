<?php

return [
    // Ordered list of gateways to try
    'gateways' => [
        'maestro',
        // add other gateways here, e.g. 'robI'
    ],

    // mapping keys to classes (can be extended)
    'classes' => [
        'maestro' => App\Services\Sms\MaestroGateway::class,
    ],
];
