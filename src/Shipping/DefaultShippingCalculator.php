<?php

namespace AscentCreative\Checkout\Shipping;

use AscentCreative\Checkout\Contracts\ShippingCalculator;

use AscentCreative\Checkout\Models\Shipping\WeightBand;
use AscentCreative\Checkout\Models\Shipping\Service;
use AscentCreative\Checkout\Basket;

class DefaultShippingCalculator implements ShippingCalculator {

    static function getCost(Service $svc, Basket $basket) {

        $weight_based =  WeightBasedShippingCalculator::getCost($svc, $basket);

        $qty_based =  QuantityBasedShippingCalculator::getCost($svc, $basket);

        // dump($svc);
        // dump($weight_based);
        // dump($qty_based);

        if(!is_null($weight_based) && !is_null($qty_based)) {
            return $weight_based + $qty_based;
        } else {
            return null;
        }

    }

}