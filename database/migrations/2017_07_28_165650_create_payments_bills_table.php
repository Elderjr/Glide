<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paymentsBills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('billId')->nullable();
            $table->foreign('billId')->references('id')->on('bills')
                    ->onDelete('set null')->onUpdate('cascade');
            $table->unsignedInteger('paymentId');
            $table->foreign('paymentId')->references('id')->on('payments')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->float('value', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paymentsBills');
    }
}
