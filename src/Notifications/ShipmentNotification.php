<?php

namespace AscentCreative\Checkout\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use AscentCreative\Checkout\Models\Shipping\Shipment;

class ShipmentNotification extends Notification
{
    use Queueable;

    private $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Shipment $shipment)
    {
        //
        $this->shipment = $shipment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        $order = $this->shipment->order;


        return (new MailMessage)
                    ->subject('Your order has been shipped :: ' . $order->orderNumber)
                    // ->line("[INSERT TABLE OF ORDER DATA]")
                    ->markdown(config('checkout.shipment_notification'), [
                         'shipment'=>$this->shipment,
                         'order'=>$order
                    ]);
                    // ->action('Download your files', url($this->order->url));
                    //->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
