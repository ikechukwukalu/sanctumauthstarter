<?php

return [
    /**
     * Pin configurations
     */
    'pin' => [
        /**
         * int - Default pin
         */
        'default' => '0000',
        /**
         * int - Uses seconds. Make sure to update the 'expires_at'
         * column if you changed this value after migration
         */
        'duration' => 300,
        /**
         * string|null - Make sure to update the 'redirect_to'
         * column if you changed this value after migration
         */
        'redirect_to' => null,
        /**
         * boolean
         */
        'verify_sender' => true,
        /**
         * string - Name of form input
         */
        'input' => '_pin',
        /**
         * string - Name of URL param
         */
        'param' => '_uuid',
        /**
         * string - Name of route
         */
        'route' => 'require_pin',
        /**
         * int - Max chars for pin
         */
        'max' => 4,
        /**
         * int - Min chars for pin
         */
        'min' => 4,
        /**
         * int|boolean - Check all or a specified number of
         * previous passwords
         */
        'check_all' => true,
        /**
         * int - Number of previous pins to check
         */
        'number' => 4,
        /**
         * int - Number of times a user is allowed to authenticate
         * using his pin
         */
        'maxAttempts' => 3,
        /**
         * int - Number of times a user is allowed to authenticate
         * using his pin
         */
        'delayMinutes' => 1,

        /**
         * Pin notification configurations
         */
        'notify' => [
            /**
             * boolean - Send a notification whenever pin is changed
             */
            'change' => true,
        ]
    ],

    /**
     * Password configurations
     */
    'password' => [
        /**
         * int|boolean - Check all or a specified number of previous passwords
         */
        'check_all' => true,
        /**
         * int - Number of previous passwords to check
         */
        'number' => 4,

        /**
         * Password notification configurations
         */
        'notify' => [
            /**
             * boolean - Send a notification whenever password is changed
             */
            'change' => true,
        ]
    ],

    /**
     * Registration configurations
     */
    'registration' => [
        /**
         * boolean - Login after registration
         */
        'autologin' => false,

        /**
         * Registration notification configurations
         */
        'notify' => [
            /**
             * boolean - Send a welcome notification after registration
             */
            'welcome' => true,
            /**
             * boolean - Send a verification email after registration
             */
            'verify' => true,
        ]
    ],

    /**
     * Login configurations
     */
    'login' => [

        /**
         * Login notification configurations
         */
        'notify' => [
            /**
             * boolean - Send a notification whenever there
             * has been a login
             */
            'user' => true,
        ]
    ],
];
