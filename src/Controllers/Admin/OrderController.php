<?php

namespace AscentCreative\Checkout\Controllers\Admin;

use AscentCreative\CMS\Controllers\AdminBaseController;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Model;
use AscentCreative\CMS\Filters\DateFilter;


use AscentCreative\Checkout\Models\Order;
use Illuminate\Support\Facades\Notification;
use AscentCreative\Checkout\Notifications\OrderConfirmation;
use AscentCreative\Checkout\Notifications\OrderNotification;

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


}