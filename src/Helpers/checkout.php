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
        // throw new Exception('CREATING BASKET');
        $basket = new Basket();
        // $basket->save();
        session(['checkout_basket'=> $basket]);
    } else {
        // check if the basket is now an order, and bin if needed.

        // dump(session('checkout_basket')->is_confirmed);
    }

	return session('checkout_basket');

}