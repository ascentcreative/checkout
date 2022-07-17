<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BasketShippingService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('checkout_orders', function (Blueprint $table) {
           
            $table->integer('shipping_service_id')->index()->after('customer_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::table('checkout_orders', function (Blueprint $table) {
           
            $table->dropColumn('shipping_service_id');

        });

    }

}
