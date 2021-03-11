<?php
use Illuminate\Support\Facades\Route;

use Illuminate\Database\Eloquent\Builder;

use AscentCreative\Checkout\Models\Basket;
use AscentCreative\Checkout\Models\Order;

//Route::get('/basket', [AscentCreative\Checkout\Controllers\BasketController::class, 'index']);
//Route::get('/basket', [App\Http\Controllers\BasketController::class, 'index']);

Route::middleware(['web'])->group(function () {

    Route::middleware(['cms-nocache'])->group(function() {

        Route::get('/basket', [AscentCreative\Checkout\Controllers\BasketController::class, 'index']);
        Route::get('/basket/clear', [AscentCreative\Checkout\Controllers\BasketController::class, 'clear']);
        Route::get('/basket/complete', [AscentCreative\Checkout\Controllers\BasketController::class, 'complete']);
        Route::get('/basket/orderconfirmed/{uuid}', [AscentCreative\Checkout\Controllers\BasketController::class, 'orderconfirmed']);
        Route::get('/basket/pollorderconfirmation/{uuid}', [AscentCreative\Checkout\Controllers\BasketController::class, 'pollorderconfirmation']);
           
    });


    Route::get('/hooktest', function() {

       $basket = Basket::where("uuid", '=', '215b53b9-7605-4e5f-8762-3df9c4c2e1ac')->first();

       $basket->confirmOrder();

//       $order = Order::where("uuid", '=', '215b53b9-7605-4e5f-8762-3df9c4c2e1ac')->first();

    });

    

});

/** outside web middleware to avoid CSRF clashes */
Route::post('/stripe/webhook', [AscentCreative\Checkout\Controllers\StripeController::class, 'webhook']);

