<?php

return [

    'global_blade' => env('CHECKOUT_GLOBAL_BLADE', 'base'),

    'global_blade_section' => env('CHECKOUT_GLOBAL_BLADE', 'pagebody'),


    /** Anonymous checkout? */
    'anonymous_checkout' => false,

    /** Payment Provider 
     * Currently supports only Stripe, but need to wire up for PayPal etc too.
    */
    'payment_provider' => 'stripe',
    'stripe_public_key' => env('STRIPE_PUBLIC', 'public_key'),
    'stripe_secret_key' => env('STRIPE_SECRET', 'secret_key'),

];
