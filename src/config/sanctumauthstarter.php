<?php

return [
    'pin' => [
        'default' => '0000', // int - Default pin
        'duration' => 300, // int - Uses seconds. Make sure to update the 'expires_at' column if you changed this value after migration
        'redirect_to' => null, // string|null - Make sure to update the 'redirect_to' column if you changed this value after migration
        'verify_sender' => true, // boolean
        'input' => '_pin', // string - Name of form input
        'param' => '_uuid', // string - Name of URL param
        'route' => 'require_pin', // string - Name of route
        'max' => 4, // int - Max chars for pin
        'min' => 4, // int - Min chars for pin
        'check_all' => true, // int|boolean - Check all or a specified number of previous passwords
        'number' => 4, // int - Number of previous pins to check
        'notify' => [
            'change' => true, // boolean - Send a notification whenever pin is changed
        ]
    ],

    'password' => [
        'check_all' => true, // int|boolean - Check all or a specified number of previous passwords
        'number' => 4, // int - Number of previous passwords to check
        'notify' => [
            'change' => true, // boolean - Send a notification whenever password is changed
        ]
    ],

    'registration' => [
        'autologin' => false, // Login after registration
        'notify' => [
            'welcome' => true, // boolean - Send a notification after registration
            'verify' => true, // boolean - Send a verification email after registration
        ]
    ],

    'login' => [
        'notify' => [
            'user' => true, // boolean - Send a notification whenever there's a login
        ]
    ],
];
