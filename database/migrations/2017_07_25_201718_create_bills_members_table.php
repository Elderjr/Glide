<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsMembersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('billsMembers', function (Blueprint $table) {
            $table->increments('id');
            $table->float('value', 8, 2);
            $table->float('contribution', 8, 2);
            $table->float('paid', 8, 2);
            $table->unsignedInteger('billId');
            $table->foreign('billId')->references('id')->on('bills')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('userId');
            $table->foreign('userId')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('billsMembers');
    }

}
