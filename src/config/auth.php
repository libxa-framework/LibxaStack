<?php

return [

    'defaults' => [
        'guard'    => 'web',
        'provider' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver'     => 'libxasecure',
            'provider'   => 'users',
            'expiration' => null,
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'database',
            'table'  => 'users',
            'model'  => \App\Models\User::class,
        ],
    ],

];
