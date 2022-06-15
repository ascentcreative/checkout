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

use AscentCreative\Transact\Contracts\iTransactable;
use AscentCreative\Transact\Traits\Transactable;

/**
 * A model to represent a customer's basket.
 */
class Basket extends OrderBase implements iTransactable
{
    use HasFactory, Transactable;

    public $consumable = ['countAll', 'summary'];
    
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

        // need to check if the item can be added
        // i.e. if a download, can only add one at a time
        // maybe there's a max per order...

        // feels like this should be farmed out to the Sellable, 
        // maybe with some form of configurable rules?

        // but in principle (and as a v1) we just need to check and then return a response.

        if ($sellable->isDownload() && $this->countOf($sellable) > 0) {
            return false;  // or should we throw an exception to allow more granular handling?
        }

        if (is_null($sku)) {
            $sku = get_class($sellable) . '_' . $sellable->id;
        } 

        $item = OrderItem::updateOrCreate([
            'order_id' => $this->id,
            'sellable_type' => get_class($sellable),
            'sellable_id' => $sellable->id,
            'sku' => $sku,
        ], [
            'title' => $sellable->getItemName(),
            'itemPrice' => $sellable->getItemPrice(),
            'purchasePrice' => $sellable->getItemPrice(),
            'qty' => 0,
        ]);

        $item->increment('qty', $qty);


        // $item = new OrderItem();
        // $item->sellable_type = get_class($sellable);
        // $item->sellable_id
        // $item->sku = $sku;
        // $item->qty = $qty;
        // $item->title = $sellable->getItemName();
        // $item->itemPrice = $sellable->getItemPrice();
        // $item->purchasePrice = $sellable->getItemPrice();
        // $this->addItem($item);
     
        return true;

    }

    private function addItem(OrderItem $item) {
       
        if (!$this->id) {
            $this->save();
        }

        $this->items()->save($item);

        BasketUpdated::dispatch($this);

    }

    public function clear() {

        $this->items()->delete();
        BasketUpdated::dispatch($this);
        session()->pull('checkout_basket');

    }

    public function remove($uuid, $qty=null) {

        $item = $this->items()->where('uuid', $uuid)->first();

        if($item) {
            $item->delete();
        }
        
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

        // repoint the transaction to the order model.
        // (Thinking this split model idea was a mistake...)
        $order = Order::find($this->id);
        $txn = $this->transaction;
        $txn->transactable()->associate($order);
        $txn->save();

        OrderConfirmed::dispatch($this);

    }



    public function getIsEmptyAttribute() {
        return $this->items()->count() == 0;
    }


    /* iTransactable */
    public function getAmount():float {
        return $this->total;
    }

    public function onPaymentConfirmed() {
        $this->confirmOrder();
    }


}
