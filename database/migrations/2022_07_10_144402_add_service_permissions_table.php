<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServicePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('checkout_shipping_service_permissions', function(Blueprint $table) {

            $table->id();

            $table->string('shippable_type')->index();
            $table->integer('shippable_id')->index();
            $table->integer("service_id")->index();
            $table->integer('subservice_id')->nullable()->index();
            $table->enum('action', ['allow','deny']);

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
        //
        Schema::drop('checkout_shipping_service_permissions');

    }
}
