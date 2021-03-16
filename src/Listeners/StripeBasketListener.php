<?php

namespace AscentCreative\Checkout\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use AscentCreative\Checkout\Events\BasketUpdated;

/**
 * Listens for BasketUpdated events and updates the Stripe PaymentIntent for the session.
 */
class StripeBasketListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
       // echo "STRIPE CONSTR";
        //exit();
    }

    /**
     * Send API Request to Stripe to manage the payment intent
     *
     * @param  object  $event
     * @return void
     */
    public function handle(BasketUpdated $event)
    {

        $secret = config('checkout.stripe_secret_key');

        $stripe = new \Stripe\StripeClient(
            $secret
             );
          
        if(!session()->has('checkout_paymentIntent')) {

            /**
            * Create a payment intent if there isn't already one in the session.
            */
            
        //    echo 'creating intent';

            $intent = $stripe->paymentIntents->create([
                'amount' => basket()->total * 100,
                'currency' => 'gbp',
                 'metadata' => [
                     'basketId' => basket()->uuid
                 ]
            ]);

            session(['checkout_paymentIntent'=> $intent]);

        } else {

            /**
            * If there is? Update it
            */

            if (basket()->total > 0) {
           //     echo 'updating intent';

                $intent = session('checkout_paymentIntent');

                
                $stripe->paymentIntents->update(
                    $intent->id,
                    [
                    'amount' => basket()->total * 100,
                    'currency' => 'gbp',
                ]);
            
            } else {

                /**
                * Is cleared basket the same thing? or would that be a new event / listener??
                */
	           
                $intent = session()->pull('checkout_paymentIntent'); // fetch (and removed) the stored intent
	             
	            $stripe->paymentIntents->cancel(
	                 $intent->id,
	                 []
	            );
	             

            }

        }

    }
}
