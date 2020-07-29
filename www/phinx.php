<?php

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
            'name' => 'png',
            'user' => 'root',
            'pass' => 'tiger',
            'port' => 3306,
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'mend-mysql',
            'name' => 'png_testing',
            'user' => 'root',
            'pass' => 'tiger',
            'port' => 3306,
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];