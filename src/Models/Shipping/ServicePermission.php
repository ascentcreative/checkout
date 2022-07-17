<?php

namespace AscentCreative\Checkout\Models\Shipping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use AscentCreative\Checkout\Models\Shipping\Service;
use AscentCreative\Checkout\Models\Shipping\SubService;


class ServicePermission extends Model {

    public $table = "checkout_shipping_service_permissions";

    public function service() {
        $this->belongsTo(Service::class);
    }

    public function subservice() {
        $this->belongsTo(SubService::class);
    }

    public function sellable() {
        $this->morphTo();
    }

}