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
use AscentCreative\Checkout\Models\Transaction;

use Carbon\Carbon;

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


    /**
     * Eloquent
     */
    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions() {
        return $this->morphMany(Transaction::class, 'transactable');
    }


    /**
     * Accessors
     */

    public function getOrderNumberAttribute() { 

        return Carbon::parse($this->confirmed_at)->format('my') . '-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);

    }

    // Use the confirmation date
    public function getOrderDateAttribute() { 

        return Carbon::parse($this->confirmed_at);

    }

    public function formatOrderDate($format="d/M/Y H:i") {
        return $this->orderDate->format($format);
    }

    public function getUrlAttribute() {
        return '/checkout/orders/' . $this->uuid;
    }


    public function getTransactionLast4Attribute() {

        $t = $this->transactions->first();
        if ($t) {
            return $t->last4;
        } else {
            return '';
        }

    }

   
    public function getTransactionRefAttribute() {

        $t = $this->transactions->first();
        if ($t) {
            return json_decode($t->data)->id;
        } else {
            return '';
        }
        

    }

   

}
