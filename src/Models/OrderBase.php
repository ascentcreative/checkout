<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

Use AscentCreative\Checkout\Models\Baase;


class OrderBase extends Base
{
    use HasFactory;


    public function items() {
        return $this->hasMany(OrderItem::class, 'order_id')->with('sellable');
    }

    public function customer() {
        return $this->morphTo();
    }

    public function getTotalQuantityAttribute() {
        return $this->items()->sum('qty');
    }

    public function getTotalAttribute() {
        $total = 0;
        foreach($this->items()->get() as $item) {
            $total += $item->purchasePrice * $item->qty;
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

    public function hasDownloadItems() {

        foreach($this->items()->with('sellable')->get() as $item) {

            if ($item->sellable->isDownload()) {
                return true;
            }

        }

        return false;

    }

    

}
