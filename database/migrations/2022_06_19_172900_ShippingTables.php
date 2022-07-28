<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShippingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('checkout_shipping_services', function (Blueprint $table) {
            $table->id();
            $table->integer('region_id'); // the world region this service relates to.
            $table->string('title')->index();
            $table->string('slug')->index();
            $table->string('description')->index();
            $table->boolean('isCollection')->index()->default(0);
            $table->timestamps();
        });

        Schema::create('checkout_shipping_regions', function (Blueprint $table) {
            // regions to ship to. Countries not a region classed as Rest of World? 
            $table->id();
            $table->string('region');
            $table->string('slug')->index();
            $table->string('description')->index();
            $table->timestamps();
        });


        Schema::create('checkout_shipping_subservices', function (Blueprint $table) {
            $table->id();
            $table->integer('service_id')->index();
            $table->string('title')->index();
            $table->string('description')->index();
            $table->timestamps();
        });


        Schema::create('checkout_shipping_weightbands', function (Blueprint $table) {
            $table->id();
            $table->integer('service_id')->index();
            $table->integer('subservice_id')->nullable();
            $table->integer('max_weight')->index();
            $table->float('cost')->index();
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
        
        Schema::drop('checkout_shipping_services');
        Schema::drop('checkout_shipping_regions');
        Schema::drop('checkout_shipping_subservices');
        Schema::drop('checkout_shipping_weightbands');

    }

}
