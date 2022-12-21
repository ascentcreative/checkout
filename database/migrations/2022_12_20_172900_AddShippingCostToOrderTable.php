<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingCostToOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('checkout_orders', function (Blueprint $table) {
            $table->float('shipping_cost')->nullable()->after('shipping_service_id')->index();
            $table->integer('shipping_service_id')->nullable()->change();
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
            $table->dropColumn('shipping_cost');
        });
        
    }

}
