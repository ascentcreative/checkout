<?php

namespace AscentCreative\Checkout\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;


/**
 * Used a convenient jumping off point for all models in the package.
 * Essentially just prefixes all child models' table names with 'checkout_'. 
 */
class Base extends Model
{
    use HasFactory;

 /*   public function getTable()
    {
        return 'checkout_' . ($this->table ?? Str::snake(Str::pluralStudly(class_basename($this))));
    } */

}
