<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class TwitterApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'twiapi';
    }
}