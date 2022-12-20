<?php
namespace AscentCreative\Checkout;

use AscentCreative\Checkout\Models\Basket;

class BasketManager {

    /** Singleton Setup */
    private static $_instance = null;

    static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new BasketManager();
        }
 
        return self::$_instance;
    }


    /** Instance elements */
    public $basket = null;

    public $items = null;

    public $customer = null;

    public $address = null;

    public $shipping_service = null;



    public function __construct() {
        
        if(!session()->has('checkout_basket_id')) {
            $this->basket = new Basket();
        } else {
            $this->basket = Basket::where('id', session('checkout_basket_id'))->with('items')->with('items.sellable')->with('customer')->first();
           
            if($this->basket == null) {
                $this->basket = new Basket();
            }

            $this->items = $this->basket->items;
            dump($this->items);
        }

    }

    public function getItems() {

        return $this->basket->items;

    }

    public function hasPhysicalItems() {
        return $this->basket->hasPhysicalItems();
    }



}

