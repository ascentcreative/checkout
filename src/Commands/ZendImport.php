<?php

namespace AscentCreative\Checkout\Commands;

use Illuminate\Console\Command;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;

use AscentCreative\Checkout\Models\Order;
use AscentCreative\Checkout\Models\OrderItem;
use AscentCreative\Checkout\Models\Customer;
use AscentCreative\Store\Models\Product;
use App\Models\User;

class ZendImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkout:zendimport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import historical order data from Zend';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // \AscentCreative\Store\ZendImporter::import();
        // dump('Importing');

        Order::truncate();
        Customer::truncate();
        OrderItem::truncate();
// return 0;

        $res = DB::connection('zend')->select('select * from store_order where status != "Precheckout"');      

        $bar = $this->output->createProgressBar(count($res));
 
        $bar->start();

        foreach($res as $zOrder) {

            $order = Order::firstOrCreate([
                'id' => $zOrder->id,
            ]);

            $order->reference = date_format(new \DateTime($zOrder->orderDate), 'y-m') . "-" . substr("0000" . $zOrder->id, -4);

            $order->created_at = $zOrder->orderDate;
            $order->confirmed_at = $zOrder->orderDate;
            $order->confirmed = 1;

            $cust = User::find($zOrder->idUser);
            if(!$cust) {
                $cust = Customer::firstOrCreate([
                    'name' => $zOrder->buyerName,
                    'email' => $zOrder->buyerEmail,
                ]);
            }

            $order->customer()->associate($cust);

            $order->shipping_cost = $zOrder->totalHandling;
            $order->uuid = $zOrder->idBasket;

            $order->save();

            $rows = DB::connection('zend')->select('select * from store_orderrow where idOrder = "' . $zOrder->id . '"');      
            // dump($rows);
            $items = [];
            foreach($rows as $row) {

                if(!$row->sku) {
                    $row->sku="unknown";
                }

                $sku = $row->sku;
                
                if(substr($sku, -2) == "-P" || substr($sku, -2) == "-D") {
                    // dump($sku);
                    $split = explode('-', $sku);
                    array_pop($split);
                    $sku = join('-', $split);
                    // dd($sku);
                }
                
                // $prod = app('product')::firstOrCreate([
                //     'sku'=>$sku,
                // ]);

                if($sku == 'unknown') {
                    $match = [
                        'title'=>$row->name,
                    ];
                } else {
                    $match = [
                        'sku' => $sku,
                    ];
                }

                $prod = app('product')->withUnpublished()->firstOrCreate(
                    $match
                , [
                    'sku'=>$sku,
                    'title'=>$row->name,
                    'is_physical'=>(substr($row->sku, -2) == "-P"),
                    'is_download'=>(substr($row->sku, -2) == "-D"),
                ]); //->where('sku', $sku)->first();

                if($sku == 'unknown') {
                    $sku = get_class($prod) . '_' . $prod->id;
                    $prod->sku = get_class($prod) . '_' . $prod->id;
                    $prod->save();
                }
              
               
                $items[] = [
                    'sellable_type'=>get_class($prod),
                    'sellable_id'=>$prod->id,
                    'sku'=>$sku,
                    'title'=>$row->name,
                    'qty'=>$row->qty,
                    'itemPrice'=>$row->unitPrice,
                ];
            }   
            $order->items()->createMany($items);

            $bar->advance();


        } 

        $bar->finish();

        return 0;
    }
}
