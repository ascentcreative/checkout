<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use AscentCreative\Checkout\Models\BasketItem;


/**
 * A model to represent a customers basket
 */
class BasketItem extends Base
{
   
    use HasFactory;


    public function sellable() {
        return $this->morphTo();
    }
   

}
