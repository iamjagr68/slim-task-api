<?php

namespace App\Controller\Task;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Entity\Task\TaskEntity;
use App\Exception\NotFoundException;

final class Create extends Base
{

    public function __invoke(Request $req, Response $res): Response
    {
        $input  = (array) $req->getParsedBody();
        $entity = new TaskEntity($input);
        $task   = $this->getTaskService()->insert($entity);

        if (!$task) {
            throw new NotFoundException();
        }

        return $this->jsonResponse($res, 201, $task);
    }

}