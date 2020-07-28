<?php

namespace App\Controller;

use Slim\Container;
use Slim\Http\Response;

abstract class BaseController
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    // Standardize the API JSON response
    protected function jsonResponse(
        Response $res,
        int $code = 200,
        $data = null
    ): Response
    {
        return $res->withJson($data, $code);
    }

}