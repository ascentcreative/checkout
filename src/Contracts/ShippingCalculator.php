<?php

namespace AscentCreative\Checkout\Contracts;

use AscentCreative\Checkout\Models\Shipping\Service;
use AscentCreative\Checkout\Basket;

interface ShippingCalculator {

    static function getCost(Service $service, Basket $basket);

}