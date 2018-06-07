<?php

namespace PsychoB\WebHook\Facades;

use PsychoB\WebHook\Services\WebHookService;
use Illuminate\Support\Facades\Facade;

class WebHook extends Facade
{
    protected static function getFacadeAccessor()
    {
        return WebHookService::class;
    }
}