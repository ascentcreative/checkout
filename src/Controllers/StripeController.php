<?php

namespace AscentCreative\Checkout\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

use AscentCreative\Checkout\Models\Basket;
use AscentCreative\Checkout\Models\Order;
use AscentCreative\Checkout\Models\Transaction;

class StripeController extends Controller
{
   
    public function webhook() {

        echo 'WEBHOOK CALLED';

         // read incoming webhook data 
        
         $webhookContent = "";
        
         $webhook = fopen('php://input' , 'rb');
         while (!feof($webhook)) {
             $webhookContent .= fread($webhook, 4096);
         }
         fclose($webhook);
         
         $event = json_decode($webhookContent);
         // process the event:
         

         if($event->type == 'payment_intent.succeeded') {
               
             // get the basket id from the posted data
               $meta = $event->data->object->metadata;
               $basket = Basket::where('uuid', '=', $meta->basketId)->first();
            

              if ($basket) { 
                $basket->confirmOrder();
                $order = Order::where('uuid', $basket->uuid)->first();
              } else {
                return response()->json(['error' => 'Basket not found - may have already been confirmed.'],404);
              }

             // \Log::info($event->data->object->charges->data[0]->balance_transaction);


             // \Log::info("FEE: " . $bt->fee);

              $t = new Transaction();
              $t->data = $webhookContent; //$event;
              $t->transactable()->associate($order);

              $t->save();



              try {

              $secret = config('checkout.stripe_secret_key');

              $stripe = new \Stripe\StripeClient(
                  $secret
                   );

              $bt = $stripe->balanceTransactions->retrieve(
                $event->data->object->charges->data[0]->balance_transaction,
                []
              );

              $t->amount = $bt->amount / 100;
              $t->fees = $bt->fee / 100;
              $t->nett = $bt->net / 100;

              $t->save();

            } catch (Exception $e) {
                \Log::error($e->getMessage());
            }

               /*
               
               $mOrder = new Ascent_Store2_Model_Order();
               
               $out = $mOrder->quoteInto('idBasket = ?', $basket);
               
               
               $mOrder = $mOrder->fetch($mOrder->quoteInto('idBasket = ?', $basket));
               
               
             
               if ($mOrder) {
               
                 if ($mOrder->paymentStatus != 'Paid'){
                   
                   $out .= "Order found - updating";
                   
                   $mOrder->paymentStatus = 'Paid';
                   $mOrder->status = "New";
                   $mOrder->paypalTxnId = $event->data->object->id;
                   $mOrder->save();
                   
                   // save payment data:
                   $mPD = new Ascent_Store2_Model_PaymentData();
                   $mPD->idOrder = $mOrder->id;
                   $mPD->data = $webhookContent;
                   $mPD->save();
                   
                   $mOrder->sendNotificationEmail();
                   $mOrder->sendConfirmationEmail();
                   
                 } else {
                   
                   $out .= "Duplicate - Order already marked paid";
                   
                 }
               
               } else {
                   
                   $out .= "No such order";
               }
         
                 $mail = new Zend_Mail();
                 $mail->addTo('kieran@ascent-creative.co.uk');
                 $mail->setFrom('no-reply@benmetcalfechef.co.uk', 'Stripe Notification');
                 $mail->setSubject('Stripe: payment_intent.succeeded');
                 $mail->setBodyText(APPLICATION_ENV . "\n\n\n" . $out . "\n\n\n" . 'Meta: ' . print_r($meta, true) . "\n\n" .  'Content: ' . print_r($event, true));
                 $mail->send();

                 */

         
         }

    }
  

}
