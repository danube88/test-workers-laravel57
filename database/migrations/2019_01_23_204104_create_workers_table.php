<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('table_number')->unique();
            $table->string('surname',255);
            $table->string('name',255);
            $table->string('patronymic',255);
            $table->date('birthday')->nullable();
            $table->integer('position_id')->unsigned();
            $table->foreign('position_id')->references('id')->on('positions');
            $table->float('salary',15,2);
            $table->date('reception_date')->nullable();
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
        Schema::dropIfExists('workers');
    }
}
