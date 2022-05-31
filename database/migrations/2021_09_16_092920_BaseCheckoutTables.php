<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BaseCheckoutTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('checkout_order_items', function(Blueprint $table) {
            $table->id();
            $table->string('uuid', 200)->index();
            $table->integer('order_id')->index();
            $table->string('sellable_type')->index();
            $table->integer('sellable_id')->index();
            $table->integer('qty');
            $table->float('itemPrice');
            $table->float('purchasePrice');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('checkout_orders', function(Blueprint $table) {
            $table->id();
            $table->string('uuid', 200)->index();
            $table->string('customer_type')->index();
            $table->integer('customer_id')->index();
            $table->integer('confirmed');
            $table->timestamp('confirmed_at');
            $table->timestamps();
        });

        Schema::create('checkout_transactions', function(Blueprint $table) {
            $table->id();
            $table->string('transactable_type')->index();
            $table->integer('transactable_id')->index();
            $table->float('amount');
            $table->longtext('data');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('checkout_order_items');
        Schema::drop('checkout_orders');
        Schema::drop('checkout_transactions');
    }
}
