<?php

namespace AscentCreative\Checkout\Models\Shipping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use AscentCreative\Checkout\Models\Shipping\Group;

class GroupItem extends Model {

    public $table = "checkout_shipping_groupitems";

    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function shippable() {
        return $this->morphTo();
    }


}