<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsMembersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('itemsMembers', function (Blueprint $table) {
            $table->increments('id');
            $table->float('distribution', 8, 2);
            $table->unsignedInteger('userId');
            $table->foreign('userId')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('itemId');
            $table->foreign('itemId')->references('id')->on('items')
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
        Schema::dropIfExists('itemsMembers');
    }

}
