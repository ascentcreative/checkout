<?php

namespace AscentCreative\Checkout\Traits;

use AscentCreative\Checkout\Contracts\Sellable;

use AscentCreative\Checkout\Models\Order;
use AscentCreative\Checkout\Models\OrderItem;

trait Customer {

    public function owns(Sellable $sellable) {

      return $this->orderItems()->where("sellable_type", get_class($sellable))->where("sellable_id", $sellable->id)->count() > 0;

    }

    public function orders() {
        return $this->morphMany(Order::class, 'customer')->with('items.sellable');
    }

    public function orderItems() {

        return $this->hasManyThrough(OrderItem::class, Order::class, 'customer_id')
                    ->where('customer_type', get_class($this))
                    ->where('confirmed', '1');

    }

}