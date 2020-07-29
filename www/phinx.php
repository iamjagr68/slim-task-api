<?php

use \Symfony\Component\Dotenv\Dotenv;

// Load up .env vars into $_ENV/$_SERVER super globals
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env.testing');

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'development',
//        'production' => [
//            'adapter' => 'mysql',
//            'host' => 'localhost',
//            'name' => 'production_db',
//            'user' => 'root',
//            'pass' => '',
//            'port' => '3306',
//            'charset' => 'utf8',
//        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => 'mend-mysql',
            'name' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_ROOT_USER'],
            'pass' => $_ENV['DB_ROOT_PASS'],
            'port' => 3306,
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'mend-mysql',
            'name' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_ROOT_USER'],
            'pass' => $_ENV['DB_ROOT_PASS'],
            'port' => 3306,
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];