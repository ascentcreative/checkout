<?php

namespace AscentCreative\Checkout\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

use AscentCreative\Checkout\Models\Basket;
use AscentCreative\Checkout\Models\Order;


class BasketController extends Controller
{

    public function consume($method) {

        if(array_search($method, basket()->consumable) !== false) {
            return response()->json(basket()->$method());
        } else {
            throw new \AscentCreative\Checkout\Exceptions\APIException('Method "' . $method . '" is not consumable');
        }
        
    }

}