<?php

namespace AscentCreative\Checkout\Models\Shipping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use AscentCreative\Checkout\Models\Shipping\Region;
use AscentCreative\Checkout\Models\Shipping\SubService;
use AscentCreative\Checkout\Models\OrderBase;

class Service extends Model {

    public $table = "checkout_shipping_services";

    public $appends = ['cost'];

    public function region() {
        return $this->belongsTo(Region::class);
    }

    public function getCost(OrderBase $basket) {
        return $this->getCostAttribute($basket);
    }

    public function getCostAttribute(OrderBase $basket = null) {

        if(is_null($basket)) {
            $basket = basket();
        }

        if(!$this->processor) {
            $proc = \AscentCreative\Checkout\Shipping\DefaultShippingCalculator::class;
        } else {
            $proc = $this->processor;
        }
        return $proc::getCost($this, $basket);

    }

    public function scopeForCountry($query, $country) {

        $query->whereHas('region', function($q) use ($country) {
            $q->whereHas('countries', function($q) use ($country) {
                $q->where('geo_countries.id', $country);
            });
        })->get();

    }

    public function subservices() {
        return $this->hasMany(SubService::class);
    }


    /**
     * 
     * *** to be implemented... ***
     * 
     * Need a way to rule out (or rule in?) certain services for products 
     * 
     * @param mixed $query
     * @param mixed $items
     * 
     * @return [type]
     */
    public function scopeForItems($query, $items) {



    }


}