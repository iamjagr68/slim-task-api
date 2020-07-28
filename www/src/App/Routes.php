<?php

use App\Controller\Task;

// Define our v1 API routes
$app->group('/v1', function () use ($app): void {

    // Define the Task routes
    $app->group('/tasks', function () use ($app): void {
        $app->get('', Task\GetAll::class);
        $app->post('', Task\Create::class);
        $app->get('/{id}',  Task\GetOne::class);
        $app->put('/{id}', Task\Update::class);
        $app->delete('/{id}', Task\Delete::class);
    });

});