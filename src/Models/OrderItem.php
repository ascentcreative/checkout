<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;



/**
 * A model to represent a customers basket
 */
class OrderItem extends Base
{
   
    use HasFactory;

    public $table = 'checkout_order_items';

    public function sellable() {
        return $this->morphTo();
    }
   
    public static function boot() {
        parent::boot();
        static::saving(function($model) {
            if(!$model->uuid) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public function isPhysical() {
        return $this->sellable->isPhysical();
    }

    public function isDownload() {
        return $this->sellable->isDownload();
    }

    public function getDownloadUrl() {
        return $this->sellable->getDownloadUrl();
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function getTotalPriceAttribute() {
        return $this->purchasePrice * $this->qty;
    }

    public function getFeesAttribute() {
        // tricky one as fees need pro-rating across items at the order level.
        // so, the order needs to calculate the splits and return just the value for this one. 
        $order = $this->order;
        $items = $order->items;
        $totalFee = $order->fees;


        $total = $order->items->sum('totalPrice');
        $fees = array();
        $feesub = 0;
        foreach($items as $item) {
            $fees[$item->id] = round(($item->totalPrice / $total) * $totalFee, 2);
            $feesub += $fees[$item->id];
        }

        $diff = $totalFee - $feesub;

        if ($diff < 0) {
            $action = 'neg';
            $diff = $diff * -1;
        } else {
            $action = 'pos';
        }
  
        $pot = $diff;

        $out = '';

        //foreach($fees as $key=>$fee) {
        $i = 0;
        $keys = array_keys($fees);
        while (round($pot,2) > 0) {

            $adj = ceil( (($fees[$keys[$i]] / $totalFee) * 100) * $diff) / 100; //ceil( ( * $diff ) / 100 );

            $out .=  '[' . $keys[$i] . "::" . $adj . ']';

            if ($action == 'pos') {
                $fees[$keys[$i]] += $adj;
            } else {
                $fees[$keys[$i]] -= $adj;
            }
            $pot = round($pot - $adj, 2);
           
            $i++;

        }

        return $fees[$this->id];

        // seems like, really, these should be inserted into the item whenever fees are logged...
    }

    public function getPriceNettOfFeesAttribute() {
        return $this->totalPrice - $this->fees;
    }
    

}
