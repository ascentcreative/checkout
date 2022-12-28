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
use AscentCreative\Checkout\Models\Shipping\Shipment;

/**
 * Triggered when a shipment is logged against an order
 */
class OrderShipment
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $shipment = null;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($payload) 
    {
        $this->shipment = $payload;
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
