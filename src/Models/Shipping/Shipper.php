<?php

namespace AscentCreative\Checkout\Models\Shipping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Shipper extends Model {

    public $table = "checkout_shippers";


    public function getTrackingUrl($ref) {
        
        if(!$this->tracking_url_format) {
            return null;
        }

        return str_replace('[ref]', $ref, $this->tracking_url_format);
    
    }

}