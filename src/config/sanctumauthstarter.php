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
         * bool - Allow a user to authenticate using the default pin
         */
        'allow_default_pin' => false,
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
     * Console command configurations
     */
    'console' => [

        /**
         * string - local backup command
         */
        'local_backup_command' => env('DB_BACKUP_COMMAND') .
                        env('DB_BACKUP_PATH') . "/" .
                        env('DB_BACKUP_FILE') . "-" .
                        date('Y-m-d_h-m-s') .
                        env('DB_BACKUP_FILE_EXT'),

        /**
         * string - remote backup command
         */
        'remote_backup_command' => "ssh -tt " .
                            env('DB_BACKUP_SSH_USER') . "@" .
                            env('DB_BACKUP_SSH_HOST') . " '" .
                            env('DB_BACKUP_COMMAND') .
                            env('DB_BACKUP_PATH') . "/" .
                            env('DB_BACKUP_FILE') . "-" .
                            date('Y-m-d_h-m-s') .
                            env('DB_BACKUP_FILE_EXT') .
                            " && exit; exec bash -l'",

        /**
         * boolean - Backup db via SSH access
         */
        'remote_access' => env('DB_REMOTE_ACCESS'),
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
