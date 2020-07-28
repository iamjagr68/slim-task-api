<?php

namespace App\Controller\Task;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Exception\NotFoundException;

final class Delete extends Base
{

    public function __invoke(
        Request $req,
        Response $res,
        array $args
    ): Response
    {
        $task_id     = (int) $args['id'];
        $success     = $this->getTaskService()->delete($task_id);

        if (!$success) {
            throw new NotFoundException();
        }

        return $this->jsonResponse($res, 204);
    }

}