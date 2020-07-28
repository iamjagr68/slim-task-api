<?php

namespace App\Handler;

use App\Exception\NotFoundException;
use Slim\Handlers\NotFound;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

final class NotFoundHandler extends NotFound
{

    public function __invoke(Request $req, Response $res): Response
    {
        throw new NotFoundException();
    }

}