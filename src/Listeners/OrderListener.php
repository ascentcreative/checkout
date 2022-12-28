<?php

namespace AscentCreative\Checkout\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

use AscentCreative\Checkout\Events\OrderConfirmed;
use AscentCreative\Checkout\Events\OrderShipment;

use AscentCreative\Checkout\Notifications\OrderConfirmation;
use AscentCreative\Checkout\Notifications\OrderNotification;
use AscentCreative\Checkout\Notifications\ShipmentNotification;

use Illuminate\Support\Facades\Log;

/**
 * Listens for user login events.
 *  - Primarily just assigns the user id to the open basket
 *  - May also need to scan contents for downloads previously bought etc (Not yet implemented)
 *  - Could even detect a user logging back in and restore an old basket... (Not yet implemented)
 */
class OrderListener
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
    public function handleConfirmed(OrderConfirmed $event)
    {

        try {
     
            Notification::send($event->order->customer, new OrderConfirmation($event->order));      
            
            $recips = config('checkout.order_notify');
            if(!is_array($recips)) {
                if ($recips == '') {
                    return 'No recipients';
                } else {
                    $recips = [$recips];
                }
            }
            
            Notification::route("mail", $recips)
                            ->notify(new OrderNotification($event->order));
                        
        } catch (\Exception $e) {
            \Log::error('Error sending emails - ' . $e->getMessage());
        }

    }



     /**
     * Send Email to the customer:
     *
     * @param  object  $event
     * @return void
     */
    public function handleShipment(OrderShipment $event)
    {

        try {
     
            Notification::send($event->shipment->order->customer, new ShipmentNotification($event->shipment));      
            
            // $recips = config('checkout.order_notify');
            // if(!is_array($recips)) {
            //     if ($recips == '') {
            //         return 'No recipients';
            //     } else {
            //         $recips = [$recips];
            //     }
            // }
            
            // Notification::route("mail", $recips)
            //                 ->notify(new OrderNotification($event->order));
                        
        } catch (\Exception $e) {
            \Log::error('Error sending emails - ' . $e->getMessage());
        }

    }

}
