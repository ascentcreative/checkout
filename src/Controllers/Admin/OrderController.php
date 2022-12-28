<?php

namespace AscentCreative\Checkout\Controllers\Admin;


use Illuminate\Support\Facades\DB;

use AscentCreative\CMS\Controllers\AdminBaseController;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Model;
use AscentCreative\CMS\Filters\DateFilter;


use AscentCreative\Checkout\Models\Order;
use Illuminate\Support\Facades\Notification;
use AscentCreative\Checkout\Notifications\OrderConfirmation;
use AscentCreative\Checkout\Notifications\OrderNotification;

use AscentCreative\Checkout\Models\Shipping\Shipment;
use AscentCreative\Checkout\Models\Shipping\ShipmentItem;

use AscentCreative\Checkout\Events\OrderShipment;


class OrderController extends AdminBaseController
{

    static $modelClass = 'AscentCreative\Checkout\Models\Order';
    static $bladePath = "checkout::admin.orders";

    public $indexSearchFields = ['customer.name', 'customer.email', 'items.title'];

    public $allowDeletions = false;

    public $indexSort = [
        ['confirmed_at', 'desc']
    ];

    public function __construct() {
        parent::__construct();
    }

    public function rules($request, $model=null) {

       return [
            'title' => 'required',
        ]; 

    }


    public function autocomplete(Request $request, string $term) {

        echo $term;

    }




    public function resendConfirmation(Order $order) {

        Notification::send($order->customer, new OrderConfirmation($order)); 

    } 


    public function resendNotification(Order $order) {

        
        // dd(config('checkout.order_notify'));

        // var_dump(config('checkout.order_notify'));
        // exit();

        $recips = config('checkout.order_notify');
        if(!is_array($recips)) {
            if ($recips == '') {
                return 'No recipients';
            } else {
                $recips = [$recips];
            }
        }
        
        Notification::route("mail", $recips)
                        ->notify(new OrderNotification($order)); 
    

    } 


    public function logShipment(Request $request, Order $order) {
        
        // validate the request:
        $form = new \AscentCreative\Checkout\Forms\Admin\Modal\LogShipment();
        $form->validate(request()->all());

        // if ok, process as a transaction:
        DB::transaction(function() use ($order, $request) {

            // 1) create a new shipment:
            $shipment = Shipment::create($request->all());

            $shipment->order()->associate($order);
            $shipment->save();

            // 2) work out what items are included:
            // - if partial, log the items from the request:
            if($request->partial_shipment) {
                // TODO
            
            } else {

                // - if not partial, get the physical items from the order, and the total qty of each:
                // foreach($order->items()->get()->groupBy('morph_key') as $group) {
                foreach($order->getUnshippedItems() as $key=>$item) {
                    $sellable = explode('_', $key);
                    // $itm = $group->first();
                    $shipment->items()->create([
                        'order_id'=>$order->id,
                        'sellable_type'=>$item->sellable_type,
                        'sellable_id'=>$item->sellable_id,
                        'qty'=>$item->qty,
                    ]);
                }
                
            }

            OrderShipment::dispatch($shipment);

        });

       
    }


}