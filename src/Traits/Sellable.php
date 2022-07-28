<?php

namespace AscentCreative\Checkout\Traits;

use AscentCreative\Checkout\Models\Order;
use AscentCreative\Checkout\Models\OrderItem;

trait Sellable {

    public function ordered() {
        return $this->morphMany(OrderItem::class, 'sellable');
    }

}