<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmihdPrioritySubAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmihd_priority_sub_areas', function (Blueprint $table) {
            $table->id();
            $table->integer('response_time');
            $table->string('response_format',20);
            $table->integer('time_solve');
            $table->string('solve_format',20);
            $table->unsignedBigInteger('dmihd_priority_id');
            $table->foreign('dmihd_priority_id')->references('id')->on('dmihd_priorities')->onDelete('cascade');
            $table->unsignedBigInteger('dmihd_sub_area_id');
            $table->foreign('dmihd_sub_area_id')->references('id')->on('dmihd_sub_areas')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dmihd_priority_sub_area');
    }
}
