<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeeColumnsToTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkout_transactions', function (Blueprint $table) {
            //
            $table->float('fees')->nullable()->after('amount');
            $table->float('nett')->nullable()->after('fees');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkout_transactions', function (Blueprint $table) {
            //
            $table->dropColumn('fees');
            $table->dropColumn('nett');

        });
    }
}
