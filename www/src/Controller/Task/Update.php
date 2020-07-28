<?php

namespace App\Controller\Task;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Entity\Task\TaskEntity;
use App\Exception\NotFoundException;

final class Update extends Base
{

    public function __invoke(
        Request $req,
        Response $res,
        array $args
    ): Response
    {
        $input       = (array) $req->getParsedBody();
        $task_id     = (int) $args['id'];
        $input['id'] = $task_id;
        $entity      = new TaskEntity($input);
        $task        = $this->getTaskService()->update($entity);

        if (!$task) {
            throw new NotFoundException();
        }

        return $this->jsonResponse($res, 200, $task);
    }

}