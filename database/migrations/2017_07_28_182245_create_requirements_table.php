<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requirements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sourceUserId');
            $table->foreign('sourceUserId')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('destinationUserId');
            $table->foreign('destinationUserId')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->float('value', 8, 2);
            $table->enum('status', ['accepted', 'rejected', 'waiting']);
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
        Schema::dropIfExists('requirements');
    }
}
