<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 60);
            $table->float('total', 8, 2);
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->date('alertDate')->nullable();
            $table->unsignedInteger('groupId')->nullable();
            $table->foreign('groupId')->references('id')->on('groups')
                    ->onDelete('set null')->onUpdate('cascade');
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
        Schema::dropIfExists('bills');
    }
}
