<?php

namespace AscentCreative\Checkout\Facades;

use Illuminate\Support\Facades\Facade;

class ShippingCalculator extends Facade 
{
    protected static function getFacadeAccessor()
    {
        return 'checkout:shippingcalculator';
    }
}