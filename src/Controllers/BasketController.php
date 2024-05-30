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

        headTitle()->add("Your Basket");

        // check if basket is currently processing
        // if so, redirect to a warning page.

        if(config('checkout.livewire_checkout')) {
            return view('checkout::basket.livewire.show');
        } else {
            return view('checkout::basket.show');
        }

    }

    /** user requested to empty the basket */
    public function clear() {

        basket()->clear();

        if (request()->wantsJson()) {
            return view('checkout::basket.contents');
        } else {
            return redirect('/basket'); 
        }

      //return redirect('/basket'); 

    }

    /**
     * Take something out of the basket.
     */
    public function remove($uuid, $qty=null) {
       
        basket()->remove($uuid, $qty);

        if (request()->wantsJson()) {
            return view('checkout::basket.contents');
        } else {
            return redirect('/basket'); 
        }

    }

     /**
     * Transact v2 endpoint:
     * 
     * @return [type]
     */
    public function transact() {
       
        return \AscentCreative\Transact\Transact::pay(basket()->commit());
 
    }



    /** 
     * User completed checkout.
     * Don't delete anything, just remove it from the session.
     */
    public function complete() {

        // basket will already have been confirmed at this point (in the Transact WebHook process)
        // so rather than a basket, we need to fetch the order;

        $order = Order::find(session('checkout_basket_id'));
        $uuid = $order->uuid;

        // redirect to view the order confirmation screen
        return redirect('/basket/orderconfirmed/' . $uuid); //->view('checkout::basket.complete')->with('order', $order);

    }


    /** display an order confirmation page to the user
     * Note: the webhook may not have been fired, so may need to return a holding status
     */
    public function orderconfirmed($uuid) {

      session()->pull('checkout_basket');

      headTitle()->add("Thank you for your order");

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
