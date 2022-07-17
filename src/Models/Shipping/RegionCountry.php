<?php

namespace AscentCreative\Checkout\Models\Shipping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use AscentCreative\Geo\Models\Country;

use AscentCreative\Checkout\Models\Shipping\Region;


class RegionCountry extends Model {

    public $table = "checkout_shipping_countries";

}