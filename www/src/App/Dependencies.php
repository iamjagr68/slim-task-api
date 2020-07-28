<?php

use App\Handler\NotFoundHandler;
use App\Handler\ApiErrorHandler;

// Set up database connection
$container['db'] = function ($container): PDO {
    $db = $container['settings']['db'];
    $pdo = new PDO(
        'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'],
        $db['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

// Define custom 404 handler
$container['notFoundHandler'] = function ($c): NotFoundHandler {
    return new NotFoundHandler();
};

// Define custom error handler
$container['errorHandler'] = function ($c): ApiErrorHandler {
    return new ApiErrorHandler($c['settings']['displayErrorDetails']);
};
