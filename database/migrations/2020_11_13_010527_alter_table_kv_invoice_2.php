<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableKvInvoice2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kiotviet_invoices', function (Blueprint $table) {
            $table->dateTime('expectedDelivery')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kiotviet_invoices', function (Blueprint $table) {
            $table->dropColumn('expectedDelivery');
        });
    }
}
