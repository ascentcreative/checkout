<?php

namespace AscentCreative\Checkout\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

use AscentCreative\Checkout\Models\Basket;
use AscentCreative\Checkout\Models\Order;

class BasketController extends Controller
{
   
    public function index() {

      return view('checkout::basket.' . (basket()->isEmpty ? 'empty' : 'contents'));

    }

    /** user requested to empty the basket */
    public function clear() {

      basket()->clear();
      return redirect('/basket'); 

    }

    /**
     * Take something out of the basket.
     */
    public function remove($uuid, $qty) {

    }


    /** 
     * User completed checkout.
     * Don't delete anything, just remove it from the session.
     */
    public function complete() {

        // check if we have an order for this basket yet.
        // if not, ask the user to refresh the page in a moment.

        // if the order has been created (by the webhook/IPN)
        // display ordered items with links to download. 

        $basket = session()->pull('checkout_basket');
        session()->pull('checkout_paymentIntent');
 
        // redirect to view the order
        // note that the order may not yet be confirmed, so view may need to handle the period between the payment and the webhook.
        // $order = Order::where('uuid', $basket->uuid);
        return redirect('/basket/orderconfirmed/' . $basket->uuid); //->view('checkout::basket.complete')->with('order', $order);

    }


    /** display an order confirmation page to the user
     * Note: the webhook may not have been fired, so may need to return a holding status
     */
    public function orderconfirmed($uuid) {

      $order = Order::where('uuid', $uuid)->first();

      if(!$order) {
        $order = Basket::where('uuid', $uuid)->first();
      }

      if (!$order) {
        return response()->json(['message' => 'Not Found!'], 404);
      }

      return view('checkout::basket.orderconfirmed')->with('order', $order);

    } 

    public function pollorderconfirmation($uuid) {

      $order = Order::where('uuid', $uuid)->first();

      if ($order) {
          return response()->json(['status' => 'confirmed']);
      }

      $basket = Basket::where('uuid', $uuid)->first();
      if ($basket) {
          return response()->json(['status' => 'unconfirmed']);
      }
     
      
      return response()->json(['status' => 'Not Found'], 404);
      
    }

}
