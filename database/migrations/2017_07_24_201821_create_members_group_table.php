<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groupMembers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('groupId');
            $table->unsignedInteger('userId');
            $table->boolean('admin');	
            $table->foreign('userId')->references('id')->on('users')
                    ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('groupId')->references('id')->on('groups')
                    ->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('membersGroup');
    }
}
