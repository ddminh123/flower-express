<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ShipperMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kiotviet_invoice_details', function (Blueprint $table) {
            $table->string('lat')->nullable();
            $table->string('lon')->nullable();
            $table->string('shipperStartTime')->nullable();
            $table->string('shipperEndTime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kiotviet_invoice_details', function (Blueprint $table) {
            //
        });
    }
}
