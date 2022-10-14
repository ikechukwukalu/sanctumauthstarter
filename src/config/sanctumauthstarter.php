<?php

return [
    'pin' => [
        'default' => '0000', // Int
        'duration' => 300, // int - Uses seconds. Make sure to update the 'expires_at' column if you changed this value after migration
        'redirect_to' => null, // string|null - Make sure to update the 'redirect_to' column if you changed this value after migration
        'verify_sender' => true, // boolean
        'input' => '_pin',
        'param' => '_uuid',
        'route' => 'require_pin',
        'max' => 4,
        'min' => 4,
        'check_all' => true, // int|boolean
        'number' => 4
    ],

    'password' => [
        'check_all' => true, // int|boolean
        'number' => 4
    ],
];
