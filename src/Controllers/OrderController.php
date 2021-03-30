<?php

namespace AscentCreative\Checkout\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use AscentCreative\Checkout\Models\Basket;
use AscentCreative\Checkout\Models\Order;



class OrderController extends Controller
{
   
    public function show(Order $order) {

        if($order->customer == Auth::user()) {

            $pageTitle = "Order " . $order->orderNumber;
            headTitle()->add($pageTitle);
    
            return view('checkout::order.show')->with('order', $order)->with('pageTitle', $pageTitle);
            

        } else {

           abort(404);
        }
    }

}
