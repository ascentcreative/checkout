<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShippingRegionCountries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

     
        Schema::create('checkout_shipping_region_countries', function (Blueprint $table) {
            // regions to ship to. Countries not a region classed as Rest of World? 
            $table->id();
            $table->integer('country_id')->index();
            $table->integer('region_id')->index();
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
        
        Schema::drop('checkout_shipping_region_countries');
        
    }

}
