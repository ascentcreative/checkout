<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShippingProcessor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('checkout_shipping_services', function (Blueprint $table) {
           
            $table->renameColumn('isCollection', 'is_collection');

        });

        Schema::table('checkout_shipping_services', function (Blueprint $table) {
           
            $table->string('processor')->nullable()->index()->after('is_collection');
          
        });

       

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::table('checkout_shipping_services', function (Blueprint $table) {
           
            $table->dropColumn('processor');

            $table->renameColumn('is_collection', 'isCollection');

        });

    }

}
