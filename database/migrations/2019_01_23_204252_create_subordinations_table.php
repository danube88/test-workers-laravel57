<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubordinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subordinations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('head_id')->unsigned();
            $table->foreign('head_id')->references('id')->on('workers');
            $table->integer('subordinate_id')->unsigned();
            $table->foreign('subordinate_id')->references('id')->on('workers');
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
        Schema::dropIfExists('subordinations');
    }
}
