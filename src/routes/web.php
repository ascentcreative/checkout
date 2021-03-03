<?php
use Illuminate\Support\Facades\Route;

//Route::get('/basket', [AscentCreative\Checkout\Controllers\BasketController::class, 'index']);
//Route::get('/basket', [App\Http\Controllers\BasketController::class, 'index']);

Route::middleware(['web'])->group(function () {

    Route::get('/basket', [AscentCreative\Checkout\Controllers\BasketController::class, 'index']);
    
});


