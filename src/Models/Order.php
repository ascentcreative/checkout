<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Str;

use AscentCreative\Checkout\Contracts\Sellable;
use AscentCreative\Checkout\Events\BasketUpdated;

/**
 * A model to represent a confirmed order.
 */
class Order extends OrderBase
{
    use HasFactory;
    
    /*
    * Uses a global scope to ensure we never include un-completed orders (baskets) when requesting orders
    */
    public $table = "checkout_orders"; 
   
    protected static function booted()
    {
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->where('confirmed', '=', '1');
        });
    }

   

}
