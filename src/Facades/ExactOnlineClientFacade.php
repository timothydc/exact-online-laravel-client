<?php

namespace PolarisDC\ExactOnline\LaravelClient\Facades;

use Illuminate\Support\Facades\Facade;

class ExactOnlineClientFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'exact-online-client';
    }
}