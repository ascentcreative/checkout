<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShipmentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('checkout_shippers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->index();
            $table->string('tracking_url_format')->nullable();
            $table->timestamps();
        });

        Schema::create('checkout_shipments', function (Blueprint $table) {
            $table->id();
            $table->integer('order_id')->index();
            $table->date('shipping_date')->index();
            $table->integer('shipper_id')->index();
            $table->string('tracking_number')->index()->nullable();
            $table->timestamps();
        });

        Schema::create('checkout_shipment_items', function (Blueprint $table) {
            $table->id();
            $table->integer('shipment_id');
            $table->integer('order_id');
            $table->string('sellable_type');
            $table->string('sellable_id');
            $table->integer('qty');
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
        Schema::drop('checkout_shipment_items');
        Schema::drop('checkout_shipments');
        Schema::drop('checkout_shippers');
        
    }

}
