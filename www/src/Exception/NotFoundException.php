<?php

namespace App\Exception;

final class NotFoundException extends ApiException
{

    public function __construct($message = "Not found")
    {
        parent::__construct($message, 404);
    }

}