<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountableToOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('checkout_order_items', function(Blueprint $table) {

            $table->dropColumn('purchasePrice');

            $table->discountable();

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('checkout_order_items', function(Blueprint $table) {

            $table->dropDiscountable();

            $table->float('purchasePrice')->after('itemPrice');

        });

    }
}
