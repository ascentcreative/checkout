<?php
namespace AscentCreative\Checkout;

use AscentCreative\Checkout\Models\Shipping\Service;

use AscentCreative\Checkout\Models\OrderItem;
use AscentCreative\Checkout\Models\Basket as BasketModel;
use AscentCreative\Checkout\Models\Order;
use AscentCreative\Checkout\Contracts\Sellable;

use AscentCreative\Checkout\BasketItem;

use Illuminate\Support\Str;

use AscentCreative\Checkout\Events\BasketUpdated;

class Basket {

    public $_items = [];

    private $_codes = [];

    private $_address;
    private $_shippingService;
    private $_shipping_cost;

    public $uuid;

    // private $customer;

    public $consumable = ['countAll', 'summary'];

    public function __construct() {
        $this->uuid = Str::uuid();
        $this->_items = collect([]);
        if(auth()->user()) {
            $this->customer = auth()->user();
        }
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

        // if there's no physical items, ditch any shpping service that may have been previously assigned:
        if(!$this->hasPhysicalItems()) {
            $this->setShippingService(null);
        }


        BasketUpdated::dispatch($this);

        return true;
       
    }

    public function clear() {
        // $this->_items = collect([]);
        basket(true);
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
        return $this->_shipping_cost;
        // if($this->_shippingService) {
        //    return $this->_shippingService->getCost($this);
        // }
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
        if(!$this->_address || !$this->_address->country_id) {
            return [];
        }

        return Service::forCountry($this->_address->country_id)->get()->whereNotNull('cost');

    }

    public function setShippingService(Service $svc=null) {
        $this->_shippingService = $svc;
        if($svc) {
            $this->_shipping_cost = $svc->getCost($this) ?? null;
        } else {
            $this->_shipping_cost = null;
        }

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
        $count = $this->countAll();

        if($this->countAll() > 0) {
            return $count . ' item' . ($count>1?'s':'') . ', &pound;' . number_format($this->totalValue(), 2);
        } else {
            return 'Basket: Empty';
        }
    }


    public function canCommit() {
        if(
            count($this->_items) == 0
           || ($this->hasPhysicalItems() && is_null($this->_shippingService))
           || ($this->hasPhysicalItems() && is_null($this->_address)) // this should be a check that we have a full address, not just a country
          )
            return false;

        return true;
    }


    /**
     * Writes the basket and associated models to the database:
     * @return BasketModel
     */
    public function commit() : BasketModel {

        // pre-commit checks:

        // is the basket in a committable state?
        //  - i.e. has items, customer, shipping svc (if needed)?
        if (!$this->canCommit()) {
            throw new \Exception("Basket cannot be committed yet");
        }

        // - does a paid order exist? If so, prevent commit
        if(Order::where('uuid', $this->uuid)->exists()) {
            throw new \Exception("Basket has been paid");
        }
        
        // - does an unpaid order exist, with an open transaction (i.e. not paid and not failed)
        //      - if so, prevent commit. 
        if(BasketModel::where('uuid', $this->uuid)->whereHas('transaction', function($q) {
            $q->whereNull('paid_at')->where('failed', 0);
        })->exists()) {
            throw new \Exception("Basket is processing");
        }

        // - does an unpaid basket exist? (i.e. all transactions are marked as failed)
        //      - Should be treated as dirty data as user may have changed order since last commit
        //      - sync data into existing basket (probably by deleting a recreating records)
        $order = BasketModel::where('uuid', $this->uuid)->first();

        if($order) {
            $order->items()->each(function($item) {
                $item->delete();
            });
            $order->address()->delete();
            $order->customer()->detach();
        } else {
            $order = new BasketModel();
        }

        // - otherwise, create fresh

        $order->fill([
            'shipping_cost'=>$this->_shipping_cost,
            'uuid'=> $this->uuid,
        ]);
        $order->shipping_service()->associate($this->_shippingService);
        $order->save();

        // rollup order items
        $grouped = $this->_items->groupBy('group_key');

        // dump($grouped);

        foreach($grouped as $group) {
            $item = $group[0]->toArray();
            $item['qty'] = $group->count();
            $out = $order->items()->create($item);
            if($item['offer_id']) {
                $out->offers()->attach(\AscentCreative\Offer\Models\Offer::find($item['offer_id']));
            }
        }

        if($this->hasPhysicalItems()) {
            $order->address()->create($this->_address->toArray());
        }
        
        return $order;
       

    }

}