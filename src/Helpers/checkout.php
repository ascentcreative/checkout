<?php 
use AscentCreative\Checkout\Models\Basket;

/** 
 * Create a new basket in the session if we don't already have one. 
 * We won't actually save it until an item is added so as not to 
 * create a load of empty baskets.
 * 
 * This means the basket is only created in the session when we call it.
 */
function basket() {
	 
    if(!session()->has('checkout_basket')) {
        $basket = new Basket();
        $basket->save();
        session(['checkout_basket'=> $basket]);
    }

	return session('checkout_basket');

}