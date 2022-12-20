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
     * Associate the basket with the current user
     *
     * Also perform checks on the basket contents to ensure the user hasn't added things to the basket which they already own
     * Not sure of the best way to inform the user at the moment... 
     *  - A service like PUSHER would be ideal for sending the notification separately to the main request
     *      but that's a lot of dev for a simple check...?
     * 
     * @param  object  $event
     * @return void
     */
    public function handle(Login $event)
    {
        
        if ($basket = basket()) {
            $basket->customer = Auth::user();
            // $basket->customer()->associate(Auth::user());
            // $basket->save();
        }

    }
}
