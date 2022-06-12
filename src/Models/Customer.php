<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Str;



/**
 * A model to represent a customer (used for anonymous baskets)
 */
class Customer extends Base
{
   
    use HasFactory, Notifiable;

    public $table = 'checkout_customers';

    public $fillable = ['name', 'email'];

 
}
