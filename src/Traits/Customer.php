<?php

namespace AscentCreative\Checkout\Traits;

use AscentCreative\Checkout\Contracts\Sellable;

use AscentCreative\Checkout\Models\Order;
use AscentCreative\Checkout\Models\OrderItem;

trait Customer {

    public function owns(Sellable $sellable) {

      return $this->orderItems()->where("sellable_type", get_class($sellable))->where("sellable_id", $sellable->id)->count() > 0;
        
        // ->whereHas('items', function($query) {
        //     $query::where("sellable_type", get_class($sellable))->where("sellable_id", $sellable-id);
        // });

    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function orderItems() {

        return $this->hasManyThrough(OrderItem::class, Order::class, 'customer_id')
                    ->where('customer_type', get_class($this))
                    ->where('confirmed', '1');

    }

}