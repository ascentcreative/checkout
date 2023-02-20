<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

Use AscentCreative\Checkout\Models\Base;
Use AscentCreative\Checkout\Models\Shipping\Service;
Use AscentCreative\Checkout\Models\Shipping\Shipment;
Use AscentCreative\Checkout\Models\Shipping\ShipmentItem;
Use AscentCreative\Geo\Traits\HasAddress;

use AscentCreative\Offer\Traits\Discountable;

class OrderBase extends Base
{
    use HasFactory, Discountable;

    public $fillable = ['id', 'shipping_cost', 'uuid', 'reference'];

    private $_items = null;

    public function items() {
        return $this->hasMany(OrderItem::class, 'order_id')->with('sellable');
    }

    public function getItemsAttribute() {
        if(is_null($this->_items)) {
            $this->_items = $this->items()->get();
        }
        return $this->_items;
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
            // $total += $this->shipping_service->getCost($this);
            $total += $this->shipping_cost;
        }

        // redundant?
        $total += $this->offerUses()->sum('value');

        return $total;
    }

    public function hasPhysicalItems() {
        return $this->countPhysicalItems() > 0;
    }

    public function countPhysicalItems() {   
        return $this->getPhysicalItems()->count();
    }

    public function physicalItemQuantity() {
        return $this->getPhysicalItems()->sum('qty');
    }

    public function getPhysicalItems() {
        return $this->items->where('sellable.is_physical', 1);
    }



    public function shipments() {
        return $this->hasMany(Shipment::class)->with('items');
    }

    public function getUnshippedItems() {

        $ordered = $this->getPhysicalItems()->groupBy('morph_key')
                        ->transform(function($item) {
                            return $item->sum('qty');
                        });

        $withSellable = $this->getPhysicalItems()->groupBy('morph_key')
        ->transform(function($item) {
            return (object) [
                'sellable' => $item->first()->sellable,
                'sellable_type' => $item->first()->sellable_type,
                'sellable_id' => $item->first()->sellable_id,
                'qty' => $item->sum('qty'),
            ];
        });

        // dump(collect($withSellable));

        $shipped = $this->shippedItems->groupBy('morph_key')
                        ->transform(function($item) {
                            return $item->sum('qty');
                        });

        $unshipped = $withSellable->filter(function($item, $key) use ($shipped) {
            return $item->qty > ($shipped[$key] ?? 0);
        })->transform(function($item, $key) use ($shipped) {
            $item->qty -= ($shipped[$key] ?? 0);
            return $item;
        })->toArray();

        // dump($unshipped);

        $this->_unshippedItems = collect($unshipped);
        return $this->_unshippedItems;

    }

    public function countUnshippedItems() {
        return $this->getUnshippedItems()->sum('qty');
    }

    public function hasUnshippedItems() {
        return $this->countUnshippedItems() > 0;
    }

    public function getShippedItems() {
        return $this->shippedItems;
    }

    public function shippedItems() {
        return $this->hasMany(ShipmentItem::class)->with('sellable');
    }

    public function hasShippedItems() {
        return $this->shippedItems->count() > 0;
    }


    public function getNeedsAddressAttribute() {
        
        return $this->hasPhysicalItems()
                && !is_null($svc = $this->shipping_service)
                && $svc->is_collection == 0;
      
    }


    public function hasDownloadItems() {
        return $this->countDownloadItems() > 0;
    }

    public function countDownloadItems() {
        return $this->getDownloadItems()->count();
    }

    public function getDownloadItems() {
        $dls = $this->items->where('sellable.is_download', 1);
        return $dls;
    }


    const UNPAID = 'ORDER_UNPAID';
    const UNSHIPPED = 'ORDER_UNSHIPPED';
    const PART_SHIPPED = 'ORDER_PART_SHIPPED';
    const COMPLETE = 'ORDER_COMPLETE';

    public function getStatusAttribute() {
        if($this->confirmed != 1) {
            return OrderBase::UNPAID;
        }

        if($this->hasPhysicalItems()) {
            if($this->hasUnshippedItems()) {
                if($this->hasShippedItems()) {
                    return OrderBase::PART_SHIPPED;
                } else {
                    return OrderBase::UNSHIPPED;
                }
            }
            // return OrderBase::UNSHIPPED;
        }

        return OrderBase::COMPLETE;
    }

    public function getStatusReadableAttribute() {

        switch($this->status) {
            case self::UNPAID:
                return 'Unpaid';
                break;
            case self::UNSHIPPED:
                return 'Awaiting Shipment';
                break;
            case self::PART_SHIPPED:
                return 'Partially Shipped';
                break;
            case self::COMPLETE:
                return 'Complete';
                break;
        }

    }


    public function getTotalWeight() {

        $ttl = 0;

        foreach($this->items()->get() as $item) {
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
