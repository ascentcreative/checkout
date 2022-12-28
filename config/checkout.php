<?php

return [

    'global_blade' => env('CHECKOUT_GLOBAL_BLADE', 'base'),

    'global_blade_section' => env('CHECKOUT_GLOBAL_BLADE', 'pagebody'),

    'basket_blade' => env('CHECKOUT_BASKET_BLADE', 'checkout::basket.layout'),
    'order_blade' => env('CHECKOUT_ORDER_BLADE', 'checkout.order.layout'),


    /** Basket Item Row */
    'basket_item_blade' => '',

    


    /** Anonymous checkout? */
    'anonymous_checkout' => true,

    'livewire_checkout' => true,

   
    'shippingcalculator' => '\AscentCreative\Checkout\Shipping\WeightBasedShippingCalculator',


    /** Email Settings */
    //Blades
    'order_confirmation' => 'checkout::order.markdown.confirmation',
    'order_notification' => 'checkout::order.markdown.notification',

    'shipment_notification' => 'checkout::shipment.markdown.notification',

    // Recipients:
    'order_notify' => '',

];
