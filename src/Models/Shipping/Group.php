<?php

namespace AscentCreative\Checkout\Models\Shipping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use AscentCreative\Checkout\Traits\Shippable;


class Group extends Model {

    use Shippable;

    public $table = "checkout_shipping_groups";


}