<?php

namespace App\Service;

abstract class BaseService
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

}