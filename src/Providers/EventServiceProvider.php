<?php

namespace AscentCreative\Checkout\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use Illuminate\Auth\Events\Login;

use AscentCreative\Checkout\Events\BasketUpdated;
use AscentCreative\Checkout\Events\OrderConfirmed;

use AscentCreative\Checkout\Listeners\StripeBasketListener;
use AscentCreative\Checkout\Listeners\OrderListener;
use AscentCreative\Checkout\Listeners\BasketLoginListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
     
    
        
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */

    public function register()
    {
        /**
         * If we're using Stripe. register the listener to update the payment intents...
         * (Not registered in the array above as it's conditional)
         */
        // if(config('checkout.payment_provider') == 'stripe') {

        //   //  echo 'reg';
        //     Event::listen(
        //         BasketUpdated::class,
        //         [StripeBasketListener::class, 'handle']
        //     );
    
        // } 
        // ** DISABLED - functionality moved to Transact ** //


        Event::listen(
            Login::class,
            [BasketLoginListener::class, 'handle']
        );

        Event::listen(
            OrderConfirmed::class,
            [OrderListener::class, 'handleConfirmed']
        );
        
    }
}
