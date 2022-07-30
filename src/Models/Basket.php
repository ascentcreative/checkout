<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Str;

use AscentCreative\Checkout\Contracts\Sellable;
use AscentCreative\Checkout\Events\BasketUpdated;
use AscentCreative\Checkout\Events\OrderConfirmed;

use AscentCreative\Checkout\Models\Shipping\Service;

use AscentCreative\Transact\Contracts\iTransactable;
use AscentCreative\Transact\Traits\Transactable;



use AscentCreative\Geo\Traits\HasAddress;

/**
 * A model to represent a customer's basket.
 */
class Basket extends OrderBase implements iTransactable
{
    use HasFactory, 
    // HasAddress,
     Transactable;


    public $consumable = ['countAll', 'summary'];

    public $codes = [];
    
    /*
    * As a basket is essentially a non-confirmed order, can we point this to the orders table.
    * Yes - and use a global scope to ensure we never include completed orders when requesting baskets
    * This then means that basket events will only be fired by the Basket object etc.
    */
    public $table = "checkout_orders"; 

    public static function boot() {
        parent::boot();
        static::saving(function($model) {

            if(is_null($model->customer)) {

                if(config('checkout.anonymous_checkout')) {
                
                    $customer = Customer::create([
                    ]);
                    $model->customer()->associate($customer);

                } else {
                    // link the order to the user

                    $usr = Auth::user();

                    if($usr) {
                        $model->customer()->associate($usr);
                    }
                
                }

            }

             
            if(!$model->uuid) {
                $model->uuid = Str::uuid();
            }

            
        });

        static::saved(function($model) {

            if(is_null($model->address)) {
                $addr = \AscentCreative\Geo\Models\Address::create([
                    'addressable_type' => get_class($model),
                    'addressable_id' => $model->id
                ]);
                $model->address = $addr;
            }

        });
    }

    /* define the relationship */
    public function address() {
    
        return $this->morphOne(\AscentCreative\Geo\Models\Address::class, 'addressable');

        // if($type) {
        //     $q = $q->where('address_type', $type);
        // }

        // return $q;
     }

   
    protected static function booted()
    {
        static::addGlobalScope('basket', function (Builder $builder) {
            $builder->where('confirmed', '!=', '1')->orWhereNull('confirmed');
        });
    }

    public function customer() {
        return $this->morphTo();
    }


    public function add(Sellable $sellable, $qty=1, $sku=null) {

        if (!$this->id) {
            $this->save();
        }

        // need to check if the item can be added
        // i.e. if a download, can only add one at a time
        // maybe there's a max per order...

        // feels like this should be farmed out to the Sellable, 
        // maybe with some form of configurable rules?

        // but in principle (and as a v1) we just need to check and then return a response.

        if ($sellable->isDownload() && $this->countOf($sellable) > 0) {
            return 'Download catch';
            return false;  // or should we throw an exception to allow more granular handling?
        }

        if (is_null($sku)) { 
           $sku = $sellable->sku ?? (get_class($sellable) . '_' . $sellable->id);
        } 

        // $item = OrderItem::firstOrCreate([
        //     'order_id' => $this->id,
        //     'sellable_type' => get_class($sellable),
        //     'sellable_id' => $sellable->id,
        //     'sku' => $sku,
        // ], [
        //     'title' => $sellable->getItemName(),
        //     'itemPrice' => $sellable->getItemPrice(),
        //     'itemPrice' => $sellable->getItemPrice(),
        //     'qty' => 0,
        // ]);

        // $item->increment('qty', $qty);

        for ($i = 0; $i < $qty; $i++) {
    
            $item = new OrderItem();
            $item->sellable_type = get_class($sellable);
            $item->sellable_id = $sellable->id;
            $item->sku = $sku;
            $item->qty = 1;
            $item->title = $sellable->getItemName();
            $item->itemPrice = $sellable->getItemPrice();
            $this->items()->save($item);

        }
     
        BasketUpdated::dispatch($this);

        return true;

    }


    public function setCode($code) {

        // initial simple version - just add the code to the internal array and fire the update event
        $this->codes = [$code]; // note, syntax tweak to only allow a single code.

        BasketUpdated::dispatch($this);

        // a more complete implementation will need the system to check that the code is valid.

    }



    private function addItem(OrderItem $item) {
       
        if (!$this->id) {
            $this->save();
        }

        $this->items()->save($item);

        BasketUpdated::dispatch($this);

    }

    public function clear() {

        foreach($this->items()->get() as $item) {
            $item->delete();
        }
   
        if($this->customer instanceof \AscentCreative\Checkout\Models\Customer) {
            $this->customer->delete();
        }
        $this->address()->delete();

        BasketUpdated::dispatch($this);
        session()->pull('checkout_basket');
        $this->delete();

    }

    public function remove($uuid, $qty=null) {

        $item = $this->items()->where('uuid', $uuid)->first();

        if($item) {
            $item->delete();
        }
        
        BasketUpdated::dispatch($this);

    }

    public function removeByKey($key) {
        $items = $this->items()->get()->where('group_key', $key)->each(function ($item) {
            $item->delete();
        });
        BasketUpdated::dispatch($this);
    }

    public function removeItem(OrderItem $item, $qty) {
        BasketUpdated::dispatch($this);
    }




    /**
     * counts how many of this Sellable are in the basket:
     */
    public function countOf(Sellable $sellable) {
        
        return $this->items()->where('sellable_type', get_class($sellable))->where('sellable_id', $sellable->id)->sum('qty');

    }

    public function countAll() {

        return $this->items()->sum('qty');

    }

    public function summary() {
        $count = $this->countAll();

        if($this->countAll() > 0) {
            return $count . ' item' . ($count>1?'s':'') . ', &pound;' . number_format($this->total, 2);
        } else {
            return 'Basket: Empty';
        }
    }

    public function confirmOrder() {

        $this->confirmed = 1;
        $this->confirmed_at = now(); //date_format(new DateTime(), 'Y-m-d H:i:s');
        $this->save();

        // // repoint the transaction to the order model.
        // // (Thinking this split model idea was a mistake...)
        $order = Order::find($this->id);
        $txn = $this->transaction;
        $txn->transactable()->associate($order);
        $txn->save();

        // do the same for the address:
        $addr = $this->address;
        $addr->addressable()->associate($order);
        $addr->save();

        // and any offers:
        $offerUses = $this->offerUses()->update(['target_type' => get_class($order)]);
        // $offerUses->target()->associate($order);

        OrderConfirmed::dispatch($this);

    }



    public function getIsEmptyAttribute() {
        return $this->items()->count() == 0;
    }

    public function getShippingQuotes($country) {


        // dd(Service::forCountry($country)->get()->whereNotNull('cost'));

        return Service::forCountry($country)->get()->whereNotNull('cost');; //ll(); //append('cost')->get();

    }


    /* iTransactable */
    public function getTransactionAmount():float {
        return $this->total;
    }

    public function onTransactionComplete() {
        $this->confirmOrder();
    }




}
