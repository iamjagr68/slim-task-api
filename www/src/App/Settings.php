<?php

return [
    'settings' => [
        'displayErrorDetails' => filter_var($_ENV['DISPLAY_ERROR_DETAILS'], FILTER_VALIDATE_BOOLEAN),
        'db' => [
            'host'   => $_ENV['DB_HOST'],
            'user'   => $_ENV['DB_USER'],
            'pass'   => $_ENV['DB_PASS'],
            'dbname' => $_ENV['DB_NAME'],
        ],
    ]
];