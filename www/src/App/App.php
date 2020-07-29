<?php

use \Symfony\Component\Dotenv\Dotenv;
use \Slim\App;

require __DIR__ . '/../../vendor/autoload.php';

// Load up .env vars into $_ENV/$_SERVER super globals
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../../.env');

// TODO: Check for required .env vars??
// TODO: Add request rate limiting??
// TODO: Add pagination

// Bootstrap our SLIM app
$settings  = require __DIR__ . '/Settings.php';
$app       = new App($settings);
$container = $app->getContainer();
require __DIR__ . '/Dependencies.php';
require __DIR__ . '/Services.php';
require __DIR__ . '/Routes.php';
