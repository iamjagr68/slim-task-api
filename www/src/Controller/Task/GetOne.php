<?php

namespace App\Controller\Task;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Exception\NotFoundException;

final class GetOne extends Base
{

    public function __invoke(
        Request $req,
        Response $res,
        array $args
    ): Response
    {
        $task_id = (int) $args['id'];
        $task    = $this->getTaskService()->getOne($task_id);

        if (!$task) {
            throw new NotFoundException();
        }

        return $this->jsonResponse($res, 200, $task);
    }

}