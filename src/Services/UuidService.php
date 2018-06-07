<?php

namespace PsychoB\WebHook\Services;

use Ramsey\Uuid\Uuid;

class UuidService
{
    public static function random(): string
    {
        return Uuid::uuid4()->getHex();
    }
}