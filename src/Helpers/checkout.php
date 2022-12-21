<?php 
use AscentCreative\Checkout\Models\Basket;
use AscentCreative\Checkout\Models\Order;
use AscentCreative\Checkout\Basket as SessionBasket;

/** 
 * Create a new basket in the session if we don't already have one. 
 * We won't actually save it until an item is added so as not to 
 * create a load of empty baskets.
 * 
 * This means the basket is only created in the session when we call it.
 */
function basket($recreate=false) {

    if($recreate || !session()->has('checkout_basket')) {
        $basket = new SessionBasket;
        session()->put('checkout_basket', $basket);
    } else {
        $basket = session('checkout_basket');
        // check the status of the basket in the DB (to ensure we're nto holding a previously paid order)
        // TODO: optimise this so the DB query only fires when a transaction has been started.
        // - Basket Commit should log the intent reference (or a flag at the very least) in the SessionBasket

        if(Order::where('uuid', $basket->uuid)->exists()) {
            //  - if we are, kill this basket and create a new one
            session()->pull('checkout_basket');
            $basket = basket(); // recursive call is easiest here.
        }
        
    }
    return $basket;

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
