<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Str;

use AscentCreative\Checkout\Contracts\Sellable;
use AscentCreative\Checkout\Events\BasketUpdated;

use AscentCreative\Checkout\Models\OrderItem;

use Carbon\Carbon;

/**
 * A model to represent a confirmed order.
 */
class Transaction extends Base
{
    use HasFactory;
    
    /*
    * Uses a global scope to ensure we never include un-completed orders (baskets) when requesting orders
    */
    public $table = "checkout_transactions"; 
   
    public $fillable = ['transactable_type', 'transactable_id', 'amount', 'data'];

    public function transactable() {
        return $this->morphTo();
    }




    public function getLast4Attribute() {
       
        return json_decode($this->data)->data->object->charges->data[0]->payment_method_details->card->last4;
    
    }
   

}
