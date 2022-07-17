<?php

namespace AscentCreative\Checkout\Models\Shipping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use AscentCreative\Geo\Models\Country;

use AscentCreative\Checkout\Models\Shipping\RegionCountry;


class Region extends Model {

    public $table = "checkout_shipping_regions";

    public function countries() {
        return $this->belongsToMany(Country::class, 'checkout_shipping_region_countries');
    }

}