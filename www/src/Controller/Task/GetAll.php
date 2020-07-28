<?php

namespace App\Controller\Task;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetAll extends Base
{

    public function __invoke(Request $req, Response $res): Response
    {
        $tasks = $this->getTaskService()->getAll();
        return $this->jsonResponse($res, 200, $tasks);
    }

}