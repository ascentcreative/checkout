<?php

namespace AscentCreative\Checkout\Shipping;

use AscentCreative\Checkout\Contracts\ShippingCalculator;

use AscentCreative\Checkout\Models\Shipping\WeightBand;
use AscentCreative\Checkout\Models\Shipping\Service;
use AscentCreative\Checkout\Models\OrderBase;

class WeightBasedShippingCalculator implements ShippingCalculator {


    static function getCost(Service $svc, OrderBase $basket) {

        $weight = $basket->getTotalWeight();

        if ($weight == 0) {
            return 0; // no weight, no cost (otherwise we get minimum weight bands returned)
            // doesn't return null as that may invalidate the shipping service.
        }

        // find the services which the products may use:
        $shippers = $basket->items()->with('sellable')->get()
                        ->where('sellable.itemWeight', '!=', 0)
                        ->pluck('sellable.shipper')
                        ->map(function($item) use ($svc) {
                            return $item->getAllowedSubservicesAttribute($svc)->pluck('id');
                        }); //.allowedSubservices');

        $ids = $shippers[0];
        for($i = 1; $i < $shippers->count(); $i++) {
            $ids = $ids->intersect($shippers[$i]);
        }

        // dd($ids);

        $band = WeightBand::where('max_weight', '>=', $weight)
                ->where('service_id', $svc->id)
                ->where(function($q) use ($ids) {
                    $q->whereNull('subservice_id')
                        ->orWhereIn('subservice_id', $ids);
                })
                
                ->orderBy('cost', 'asc')
                ->first();

        if ($band) {
            return $band->cost;
        } else {
            return null;
        }

    }

    static function getQuotes($country) {

        $weight = basket()->getTotalWeight();

        // get all the services which operate in the region:
        // (and which the items allow the use of - to be implemented)
        $svcs = Service::whereHas('region', function($q) use ($country) {
                    $q->whereHas('countries', function($q) use ($country) {
                        $q->where('geo_countries.id', $country);
                    });
                })->get();

        // dd($svcs);

        $bands = [];
        foreach($svcs as $svc) {

            $band = WeightBand::where('max_weight', '>=', $weight)
                                ->where('service_id', $svc->id)
                                ->orderBy('cost', 'asc')
                                ->first();

            if($band) {
                $bands[] = $band;
            }

        }

        return $bands;


    }

}