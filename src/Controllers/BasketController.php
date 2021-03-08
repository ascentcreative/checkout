<?php

namespace AscentCreative\Checkout\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class BasketController extends Controller
{
   
    public function index() {

      return view('checkout::basket.contents');

    }

}
