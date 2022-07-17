<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

Use AscentCreative\Checkout\Models\Base;
Use AscentCreative\Checkout\Models\Shipping\Service;
Use AscentCreative\Geo\Traits\HasAddress;


class OrderBase extends Base
{
    use HasFactory;


    public function items() {
        return $this->hasMany(OrderItem::class, 'order_id')->with('sellable');
    }

    public function customer() {
        return $this->morphTo();
    }


    public function shipping_service() {
        return $this->belongsTo(Service::class); //, 'id', 'shipping_service_id');
    }

    public function getTotalQuantityAttribute() {
        return $this->items()->sum('qty');
    }

    public function getItemTotalAttribute() {
        $total = 0;
        foreach($this->items()->get() as $item) {
            $total += $item->itemPrice * $item->qty;
        }
        return $total;
    }

    public function getTotalAttribute() {
        $total = 0;
        $total += $this->itemTotal;
        if($this->shipping_service) {
            $total += $this->shipping_service->getCost($this);
        }
        return $total;
    }

    public function hasPhysicalItems() {
        
        foreach($this->items()->with('sellable')->get() as $item) {

            if ($item->sellable->isPhysical()) {
                return true;
            }

        }

        return false;

    }

    public function getNeedsAddressAttribute() {
        
        return $this->hasPhysicalItems()
                && !is_null($svc = $this->shipping_service)
                && $svc->is_collection == 0;
      
    }


    public function hasDownloadItems() {

        foreach($this->items()->with('sellable')->get() as $item) {

            if ($item->sellable->isDownload()) {
                return true;
            }

        }

        return false;

    }



    public function getTotalWeight() {

        $ttl = 0;

        foreach($this->items()->with('sellable')->get() as $item) {
            $ttl += $item->sellable->itemWeight * $item->qty;
        }

        return $ttl;
    }
    

}
