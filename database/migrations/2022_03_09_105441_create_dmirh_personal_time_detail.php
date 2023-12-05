<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmirhPersonalTimeDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_personal_time_detail', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->unsignedInteger('dmirh_personal_time_id')->nullable();;
            $table->foreign('dmirh_personal_time_id')->references('id')->on('dmirh_personal_time')->onDelete('cascade');
            $table->tinyInteger('week_day');
            $table->time('entry_hour');
            $table->time('exit_food_hour');
            $table->time('entry_food_hour');
            $table->time('exit_hour');
            $table->boolean('deleted')->default(0);
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
        Schema::dropIfExists('dmirh_personal_time_detail');
    }
}
