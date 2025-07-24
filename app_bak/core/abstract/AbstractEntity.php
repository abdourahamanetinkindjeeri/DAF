<?php

namespace App\Core\abstract;



abstract class AbstractEntity
{

    abstract static public function toObject(array $row): static;


    abstract function toArray(): array;
    function toJson(): string
    {
//        return json_encode(self::toArray());
        return json_encode(static::toArray());
    }
}