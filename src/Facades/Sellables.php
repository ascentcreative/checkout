<?php

namespace AscentCreative\Checkout\Facades;

use Illuminate\Support\Facades\Facade;

class Sellables extends Facade 
{
    protected static function getFacadeAccessor()
    {
        return 'checkout:sellables';
    }
}