<?php
namespace AscentCreative\Checkout;

use AscentCreative\Checkout\Contracts\Sellable;

// We use Model to get access to the Attributes and ArrayAccess stuff
// but don't set any tables or relationships.
// This was a shortcut to avoiod writing a new SessionModel parent.
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;


class BasketItem extends Model {

    
    // public function __construct($input) {
    //     $this->fill($input);
    // }

    public function fill($ary) {
        foreach($ary as $key=>$value) {
            $this->$key = $value;
        }
    }

    public function getGroupKeyAttribute() {
        $array = [
            $this->sku,
            $this->offer_id ?? '-',
            $this->itemPrice,
        ];
        return join('_', $array);
    }

    public function getMorphKeyAttribute() {
        return $this->sellable_type . "_" . $this->sellable_id;
    }

    public function getSellableAttribute() {
        $cls = $this->sellable_type;
        return $cls::find($this->sellable_id);
    }

    public function getOfferAttribute() {
        return \AscentCreative\Offer\Models\Offer::find($this->offer_id);
    }
 
}