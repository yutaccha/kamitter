<?php

namespace App\Http\Components;

class TwitterApi
{
    public static function sayHello(){
        return config('services.twitter')['client_id'];
    }
}