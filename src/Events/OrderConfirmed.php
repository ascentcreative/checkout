<?php

namespace AscentCreative\Checkout\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use AscentCreative\Checkout\Models\Basket;
use AscentCreative\Checkout\Models\Order;

/**
 * Triggered whenever the Basket model is updated (add / remove items, coupon codes, shipping details etc)
 */
class OrderConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order = null;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($payload) 
    {

        if (is_string($payload)) {
            // assume UUID - fetch order using that
            $this->order = Order::where('uuid', $payload)->first();
        } else {
           switch(get_class($payload)) {
                case 'AscentCreative\Checkout\Models\Basket':
                    $this->order = Order::where('uuid', $payload->uuid)->first();
                    break;

                case 'AscentCreative\Checkout\Models\Order':
                    $this->order = $payload;
           }
        }

        if (is_null($this->order)) {
            throw new Exception('order not found');
        }
        
            
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
  /*  public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    } */
}
