<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('payerUserId');
            $table->foreign('payerUserId')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('receiverUserId');
            $table->foreign('receiverUserId')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->float('value', 8, 2);
            $table->enum('status', ['confirmed', 'canceled']);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
