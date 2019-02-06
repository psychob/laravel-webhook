<?php

namespace PsychoB\WebHook\Facades;

use PsychoB\WebHook\Services\WebHookService;
use Illuminate\Support\Facades\Facade;
use PsychoB\WebHook\Testing\FakeWebHookService;

class WebHook extends Facade
{
    public static function fake()
    {
        static::swap(new FakeWebHookService());
    }

    protected static function getFacadeAccessor()
    {
        return WebHookService::class;
    }
}
