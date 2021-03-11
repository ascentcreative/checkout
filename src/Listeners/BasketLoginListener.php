<?php

namespace AscentCreative\Checkout\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

/**
 * Listens for user login events.
 *  - Primarily just assigns the user id to the open basket
 *  - May also need to scan contents for downloads previously bought etc (Not yet implemented)
 *  - Could even detect a user logging back in and restore an old basket... (Not yet implemented)
 */
class BasketLoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        
       // echo "LISTEBNER CONSTR";
       // exit();
    }

    /**
     * Send API Request to Stripe to manage the payment intent
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Login $event)
    {
        
        if ($basket = basket()) {
            $basket->customer()->associate(Auth::user());
            $basket->save();
        }

    }
}
