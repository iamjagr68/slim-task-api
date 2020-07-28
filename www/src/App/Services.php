<?php

use App\Service\Task\TaskService;
use Slim\Container;

$container['task_service'] = function (Container $container): TaskService {
    return new TaskService($container->get('db'));
};