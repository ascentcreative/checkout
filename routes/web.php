<?php
use Illuminate\Support\Facades\Route;

use Illuminate\Database\Eloquent\Builder;

use AscentCreative\Checkout\Models\Basket;
use AscentCreative\Checkout\Models\Order;

use AscentCreative\Checkout\Controllers\Admin\OrderController;
use AscentCreative\Checkout\Notifications\OrderConfirmation;

//Route::get('/basket', [AscentCreative\Checkout\Controllers\BasketController::class, 'index']);
//Route::get('/basket', [App\Http\Controllers\BasketController::class, 'index']);

Route::middleware(['web'])->group(function () {

    Route::middleware(['cms-nocache'])->group(function() {

        Route::get('/basket', [AscentCreative\Checkout\Controllers\BasketController::class, 'index']);
        Route::get('/basket/clear', [AscentCreative\Checkout\Controllers\BasketController::class, 'clear']);
        Route::get('/basket/remove/{uuid}/{qty?}', [AscentCreative\Checkout\Controllers\BasketController::class, 'remove']);
        Route::get('/basket/complete', [AscentCreative\Checkout\Controllers\BasketController::class, 'complete']);
        Route::get('/basket/orderconfirmed/{uuid}', [AscentCreative\Checkout\Controllers\BasketController::class, 'orderconfirmed']);
        Route::get('/basket/pollorderconfirmation/{uuid}', [AscentCreative\Checkout\Controllers\BasketController::class, 'pollorderconfirmation']);

        Route::post('/basket/transact', function() {
            return \AscentCreative\Transact\Transact::start(basket());
        });
           
    });


    Route::middleware(['auth'])->group(function () {

        Route::get('/checkout/orders', [AscentCreative\Checkout\Controllers\OrderController::class, 'index'])->name('checkout-all-orders');
        Route::get('/checkout/orders/{order:uuid}', [AscentCreative\Checkout\Controllers\OrderController::class, 'show']);
        Route::get('/checkout/orders/{order:uuid}/resend', function (Order $order) { 
            Notification::send($order->customer, new OrderConfirmation($order));   
        });

        Route::get('/checkout/orders/{order:uuid}/testemail', function (Order $order) {

            $mail = new OrderConfirmation($order);
            return $mail->toMail('a@b.com');

        });

    });

    Route::get('/hooktest', function() {

       $basket = Basket::where("uuid", '=', '215b53b9-7605-4e5f-8762-3df9c4c2e1ac')->first();

       $basket->confirmOrder();

//       $order = Order::where("uuid", '=', '215b53b9-7605-4e5f-8762-3df9c4c2e1ac')->first();

    });

    /**
     * 'API' routes:
     */
    Route::prefix('/checkout/api')->group(function() {

        Route::get('/basket/{method}', [AscentCreative\Checkout\Controllers\API\BasketController::class, 'consume']);

    });


    Route::prefix('/admin')->middleware(['auth', 'can:administer'])->group(function() {

        Route::get('/orders/{order}/delete', [AscentCreative\Checkout\Controllers\Admin\OrderController::class, 'delete']);
        Route::resource('/orders', OrderController::class);

        Route::get('/allintents', function() {

            $secret = config('checkout.stripe_secret_key');

            $stripe = new \Stripe\StripeClient(
                $secret
                 );
                
            return ($stripe->paymentIntents->all());

        });

        // Route::get('/updateTransactions', function() {

        //     $secret = config('checkout.stripe_secret_key');

        //     $stripe = new \Stripe\StripeClient(
        //         $secret
        //          );


        //     foreach(AscentCreative\Checkout\Models\Transaction::all() as $trans) {

        //         $bt_ref = json_decode($trans->data)->data->object->charges->data[0]->balance_transaction;


        //         dump($bt_ref);

        //         try {

        //          $bt = $stripe->balanceTransactions->retrieve(
        //             $bt_ref,
        //             []
        //         );

        //         $trans->amount = $bt->amount / 100;
        //         $trans->fees = $bt->fee / 100;
        //         $trans->nett = $bt->net / 100;

        //         $trans->save();

        //     } catch (Exception $e) {
        //         dump($e);
        //     }

        //     }

        // });

    });
    

});

/** outside web middleware to avoid CSRF clashes */
Route::post('/stripe/webhook', [AscentCreative\Checkout\Controllers\StripeController::class, 'webhook']);



