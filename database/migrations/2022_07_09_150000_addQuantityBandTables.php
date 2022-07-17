<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityBandTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('checkout_shipping_quantitybands', function (Blueprint $table) {
           
            $table->id();

            $table->string('shippable_type')->nullable()->index();
            $table->integer('shippable_id')->nullable()->index();
            $table->integer('service_id')->index();
            $table->integer('subservice_id')->nullable()->index();
            $table->integer('minQty')->default(1)->index();
            $table->float('cost_each')->index();

            $table->timestamps();

        });

        Schema::create('checkout_shipping_groups', function(Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->index();

            $table->timestamps();
        });

        Schema::create('checkout_shipping_groupitems', function(Blueprint $table) {

            $table->id();

            $table->integer('shippinggroup_id')->index();
            $table->string('shippable_type')->index();
            $table->integer('shippable_id')->index();

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
        
        Schema::drop('checkout_shipping_quantitybands');

        Schema::drop('checkout_shipping_groups');

        Schema::drop('checkout_shipping_groupitems');



    }

}
