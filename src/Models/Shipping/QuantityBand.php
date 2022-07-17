<?php

namespace AscentCreative\Checkout\Models\Shipping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use AscentCreative\Checkout\Models\Shipping\Service;
use AscentCreative\Checkout\Models\Shipping\SubService;


class QuantityBand extends Model {

    public $table = "checkout_shipping_quantitybands";

    public function service() {
        return $this->belongsTo(Service::class);
    }

    public function subservice() {
        return $this->belongsTo(SubService::class);
    }

}