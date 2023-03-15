<?php

return [
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
        ],
        /**
         * int - Number of times a user is allowed to authenticate
         * when trying to login
         */
        'maxAttempts' => 3,
        /**
         * int - Number of times a user is allowed to authenticate
         * when trying to login
         */
        'delayMinutes' => 1,
    ],

    /**
     * Cookie configurations
     */
    'cookie' => [
        /**
         * int - Uses minutes.
         */
        'minutes' => 5,

        /**
         * string - Cookie name.
         */
        'name' => 'user_uuid'
    ],
];
