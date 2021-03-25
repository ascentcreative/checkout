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
    

}
