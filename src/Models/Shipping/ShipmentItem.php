<?php

namespace AscentCreative\Checkout\Models\Shipping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class ShipmentItem extends Model {

    public $table = "checkout_shipment_items";

    public $fillable = ['shipment_id', 'order_id', 'sellable_type', 'sellable_id', 'qty'];

    public function sellable() {
        return $this->morphTo();
    }

    public function getMorphKeyAttribute() {
        return morphKey($this, 'sellable');
    }

}