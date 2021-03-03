<?php

namespace AscentCreative\Checkout\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class BasketController extends Controller
{
   
    public function index() {

      
      echo 'BASKET';

      print_r(basket());

      foreach(basket()->items()->get() as $item) {

        echo '<P>' . $item->sellable->getItemName() . ' (' . $item->qty . ' @ ' . $item->sellable->getItemPrice() . ')</P>';

      }

      echo 'END';
    }

}
