<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmirhAttendancePolicy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_attendance_policy', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->tinyInteger('tolerance');
            $table->tinyInteger('delay');
            $table->tinyInteger('puntuality');
            $table->tinyInteger('suspension');
            $table->string('location');
            $table->boolean('deleted');
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
        Schema::dropIfExists('dmirh_attendance_policy');
    }
}
