<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use AscentCreative\Checkout\Contracts\Sellable;

/**
 * A model to represent a customers basket
 */
class Basket extends Base
{
   
    use HasFactory;

    public function add(Sellable $sellable, $qty=1) {
        $item = new BasketItem();
        //$item->sellable($sellable);
        $item->sellable_type = get_class($sellable);
        $item->sellable_id = $sellable->id;
        $item->qty = $qty;
        $this->addItem($item);
        //$item->qt
    }

    public function addItem(BasketItem $item) {
        if (!$this->id) {
            $this->save();
        }

        $this->items()->save($item);
    }

    public function items() {
        return $this->hasMany(BasketItem::class);
    }

    public function getTotalAttribute() {
        $total = 0;
        foreach($this->items()->get() as $item) {
            $total += $item->sellable->getItemPrice() * $item->qty;
        }
        return $total;
    }
    

}
