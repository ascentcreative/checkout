<?php
namespace AscentCreative\Checkout;

use AscentCreative\Checkout\Models\Shipping\Service;

use AscentCreative\Checkout\Models\OrderItem;
use AscentCreative\Checkout\Models\Basket as BasketModel;
use AscentCreative\Checkout\Contracts\Sellable;

use AscentCreative\Checkout\BasketItem;

use AscentCreative\Checkout\Events\BasketUpdated;

class Basket {

    public $_items = null;

    private $_codes = [];

    private $_address;
    private $_shippingService;

    private $_customer;

    public $consumable = ['countAll', 'summary'];

    public function __construct() {
        $this->_items = collect([]);
    }

    /**
     * Add a quantity of a certain sellable item to the basket
     * 
     * @param Sellable $sellable
     * @param mixed $qty=1
     * @param mixed $sku=null
     * 
     * @return [type]
     */
    public function add(Sellable $sellable, $qty=1, $sku=null) {

        if (is_null($sku)) { 
            $sku = $sellable->sku ?? (get_class($sellable) . '_' . $sellable->id);
        } 

        if ($sellable->isDownload()) {
            
            if($this->countOf($sellable) > 0) {
            // return 'Download catch';
                return false;  // or should we throw an exception to allow more granular handling?
            } else {
                // not already in the basket
                // ensure the quantity can only be 1 - should be controlled by the UI, but just to be safe!
                $qty = 1;
            }
        }
        
        for($i = 0; $i < $qty; $i++) {
            $item = new BasketItem([
                'sellable_type' => get_class($sellable),
                'sellable_id' => $sellable->id,
                'sku' => $sku,
                'title' => $sellable->getItemName(),
                'itemPrice' => $sellable->getItemPrice(),
                'original_price' => $sellable->getItemPrice(),
                'is_physical' => $sellable->is_physical,
                'is_download' => $sellable->is_download,
                'item_weight' => $sellable->itemWeight,
                'qty'=>1,
            ]);
            $this->_items->push($item);
        }

        BasketUpdated::dispatch($this);

        return true;

    }


    /**
     * Remove a quantity of a given sellable from the basket. If qty=null, removes all of that sellable.
     * 
     * @param Sellable $sellable
     * @param mixed $qty=null
     * 
     * @return [type]
     */
    public function remove(Sellable $sellable, $qty=null) {

        $forgettable = $this->_items->where('sellable_type', get_class($sellable))->where('sellable_id', $sellable->id);
        
        if(!is_null($qty)) {
            $forgettable = $forgettable->take($qty);
        }

        $this->_items = $this->_items->except($forgettable->keys());

        BasketUpdated::dispatch($this);

        return true;
       
    }

    public function clear() {
        $this->_items = collect([]);
    }

    public function setQuantityByKey($key, $qty) {
      
        $items = $this->_items->where('sku', $key);

        $basket_qty = $items->sum('qty');

        if($qty < $basket_qty) {
            // target qty is fewer than we currently have - remove extras
            $this->remove($items->first()->sellable, $basket_qty - $qty);
        }

        if($qty > $basket_qty) {
            // target qty is greater than we currently have - add more
            $this->add($items->first()->sellable, $qty - $basket_qty);
        }

        // NB - doesn't need to fire the updated event as add() and remove() already do.

    }


    public function totalValue() {
        return $this->itemTotal() + $this->shippingTotal();
    }


    public function isEmpty() {
        return $this->_items->count() == 0;
    }

    public function hasPhysicalItems() {

        return $this->_items->pluck('is_physical')->contains(1);
        // return true;
    }

    public function items() {
        return $this->_items;
    }

    public function codes() {
        return $this->_codes;
    }

    public function itemTotal() {
        return $this->_items->sum('itemPrice');
    }

    public function shippingTotal() {
        if($this->_shippingService) {
           return $this->_shippingService->getCost($this);
        }
    }


    /** shipping address */
    // public function setShippingAddress($addr) {
    //     $this->_address = $addr;
    //     return $this;
    // }

    public function getShippingAddress() {
        if(is_null($this->_address))
            $this->_address = new \AscentCreative\Geo\Models\Address();

        return $this->_address;
    }


    /**
     * Get relevant shipping quotes
     */
    public function getShippingQuotes() {

        // dd(Service::forCountry($country)->get()->whereNotNull('cost'));
        if(!$this->_address->country_id) {
            return [];
        }

        return Service::forCountry($this->_address->country_id)->get()->whereNotNull('cost');

    }

    public function setShippingService(Service $svc=null) {
        $this->_shippingService = $svc;
        return $this;
    }
    
    public function getShippingService() {
        return $this->_shippingService;
    }

    public function needsAddress() {
        
        return $this->hasPhysicalItems()
                && !is_null($svc = $this->_shippingService)
                && $svc->is_collection == 0;
      
    }

    public function getTotalWeight() {

        $ttl = 0;

        $ttl = $this->_items->sum('item_weight');

        return $ttl;
    }

    /**
     * counts how many of this Sellable are in the basket:
     */
    public function countOf(Sellable $sellable) {
        
        return $this->_items->where('sellable_type', get_class($sellable))->where('sellable_id', $sellable->id)->sum('qty');

    }

    /**
     * Total Item Quantity
     */
    public function countAll() {

        return $this->_items->sum('qty');

    }


    /**
     * Quick Summary String of basket value and size
     * 
     * @return [type]
     */
    public function summary() {
        return '123';
        $count = $this->countAll();

        if($this->countAll() > 0) {
            return ''; //$count . ' item' . ($count>1?'s':'') . ', &pound;' . number_format($this->totalValue(), 2);
        } else {
            return 'Basket: Empty';
        }
    }



    /**
     * Writes the basket and associated models to the database:
     * @return BasketModel
     */
    public function commit() : BasketModel {

        $order = new BasketModel();
        $order->save();

    

        // rollup order items
        $grouped = $this->_items->groupBy('group_key');

        dump($grouped);


        // $order->items()->createMany($this->_items->map(function($value) { 
        //         return $value->toArray();
        //     })
        // );

        $order->address()->create($this->_address->toArray());

        return $order;
       

    }

}