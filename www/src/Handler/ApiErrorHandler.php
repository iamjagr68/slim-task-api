<?php

namespace App\Handler;

use Slim\Handlers\Error;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Exception\ApiException;

final class ApiErrorHandler extends Error
{

    public function __invoke(
        Request $req,
        Response $res,
        \Exception $e
    ): Response
    {
        $statusCode = $this->getStatusCode($e);
        $error      = [
            'message' => 'An error occurred',
        ];

        // Only if the exception is an instance of APIException
        // or if display error details is turned on
        // do we display the exception message
        if ($e instanceof ApiException) {
            $error['message'] = $e->getMessage();
        } elseif ($this->displayErrorDetails) {
            $error['exception'] = [];

            do {
                $error['exception'][] = [
                    'type' => get_class($e),
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => explode("\n", $e->getTraceAsString()),
                ];
            } while ($exception = $e->getPrevious());
        }

        return $res
            ->withStatus($statusCode)
            ->withHeader('Content-type', 'application/problem+json')
            ->write(json_encode($error, JSON_UNESCAPED_SLASHES));
    }

    // Attempt to get status code from throw Exception
    private function getStatusCode(\Exception $e): int
    {
        $statusCode    = 500;
        $exceptionCode = $e->getCode();
        if (
            is_int($exceptionCode) &&
            $exceptionCode >= 400 &&
            $exceptionCode <= 599
        ) {
            $statusCode = $exceptionCode;
        }

        return $statusCode;
    }

}