<?php

namespace AscentCreative\Checkout\Contracts;

use AscentCreative\Checkout\Models\Shipping\Service;
use AscentCreative\Checkout\Models\OrderBase;

interface ShippingCalculator {

    static function getCost(Service $service, OrderBase $basket);

}