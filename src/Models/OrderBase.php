<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

Use AscentCreative\Checkout\Models\Baase;


class OrderBase extends Base
{
    use HasFactory;


    public function items() {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function customer() {
        return $this->morphTo();
    }


}
