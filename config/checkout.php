<?php

return [

    'global_blade' => env('CHECKOUT_GLOBAL_BLADE', 'base'),

    'global_blade_section' => env('CHECKOUT_GLOBAL_BLADE', 'pagebody'),


    /** Basket Item Row */
    'basket_item_blade' => '',

    


    /** Anonymous checkout? */
    'anonymous_checkout' => false,

    'livewire_checkout' => false,

   
    'shippingcalculator' => '\AscentCreative\Checkout\Shipping\WeightBasedShippingCalculator',


    /** Email Settings */
    //Blades
    'order_confirmation' => 'checkout::order.markdown.confirmation',
    'order_notification' => 'checkout::order.markdown.notification',

    // Recipients:
    'order_notify' => '',

];
