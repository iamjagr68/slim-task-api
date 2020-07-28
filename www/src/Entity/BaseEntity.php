<?php

namespace App\Entity;

abstract class BaseEntity implements \JsonSerializable
{
    abstract public function jsonSerialize(): array;
}