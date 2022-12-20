<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

Use AscentCreative\Checkout\Models\Base;
Use AscentCreative\Checkout\Models\Shipping\Service;
Use AscentCreative\Geo\Traits\HasAddress;

use AscentCreative\Offer\Traits\Discountable;

class OrderBase extends Base
{
    use HasFactory, Discountable;


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

        $disc = $this->itemOfferUses()->sum('value');
        $total += $disc; // $disc will be -ve;

        return $total;
    }

    public function getTotalAttribute() {
        $total = 0;
        $total += $this->itemTotal;
        if($this->shipping_service) {
            $total += $this->shipping_service->getCost($this);
        }

        $total += $this->offerUses()->sum('value');

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
    

    public function itemOfferUses() {

        $itemIds = $this->items()->get()->pluck('id')->toArray();

        $uses = \AscentCreative\Offer\Models\OfferUse::where('target_type', OrderItem::class)
                                        ->whereIn('target_id', $itemIds);


        return $uses;

    }

}
