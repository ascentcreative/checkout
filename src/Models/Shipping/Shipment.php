<?php

namespace AscentCreative\Checkout\Models\Shipping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use AscentCreative\Checkout\Models\Shipping\ShipmentItem;
use AscentCreative\Checkout\Models\Shipping\Shipper;
use AscentCreative\Checkout\Models\Order;

class Shipment extends Model {

    public $table = "checkout_shipments";

    public $fillable = ['shipping_date', 'shipper_id', 'tracking_number'];

    public function shipper() {
        return $this->belongsTo(Shipper::class);
    }

    public function items() {
        return $this->hasMany(ShipmentItem::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

}