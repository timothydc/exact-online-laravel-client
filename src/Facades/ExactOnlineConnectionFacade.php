<?php

namespace PolarisDC\Exact\ExactOnlineConnector\Facades;

use Illuminate\Support\Facades\Facade;


class ExactOnlineConnectionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'exact-online-connection';
    }
}