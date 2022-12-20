<?php 
use AscentCreative\Checkout\Models\Basket;
use AscentCreative\Checkout\Basket as SessionBasket;

/** 
 * Create a new basket in the session if we don't already have one. 
 * We won't actually save it until an item is added so as not to 
 * create a load of empty baskets.
 * 
 * This means the basket is only created in the session when we call it.
 */
function basket() {

    if(!session()->has('checkout_basket')) {
        session()->put('checkout_basket', new SessionBasket);
    }
    return session('checkout_basket');



    $basketManager = AscentCreative\Checkout\BasketManager::getInstance();

    // // dump($basket->id);
    // // dd($basket->id);

    // // dump(session('checkout_basket_id'));
    // // session()->pull('checkout_basket_id');

    // // No stored id, so we'll just return a dummy one which might get saved later.
    // if(!session()->has('checkout_basket_id')) {
    //     $basket = new Basket();
    //     // dd($basket);
    // } else {
    //     $basket = Basket::where('id', session('checkout_basket_id'))->with('items')->with('customer')->first();
    // }

    return $basketManager->basket;
	// return session('checkout_basket');

}
