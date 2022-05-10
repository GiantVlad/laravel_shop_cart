<?php

return [
    'default' => 'paypal',
    'methods' => [
        'paypal' => [
            'label' => 'PayPal',
            'class_name' => 'App\\Services\\Payment\\PaypalPayment',
            'client_id' => env('PAYPAL_CLIENT_ID', '0000'),
            'secret' => env('PAYPAL_CLIENT_SECRET', 'secret'),
            'settings' => [
                'mode' => 'sandbox', // live, sandbox
                'http.ConnectionTimeOut' => 1000,
                'log.LogEnabled' => true,
                'log.FileName' => storage_path() . '/logs/paypal.log',
                'log.LogLevel' => 'FINE',
            ],
        ],
        'fondy' => [
            'label' => 'Fondy',
            'class_name' => 'App\\Services\\Payment\\FondyPayment',
        ],
    ],
];
