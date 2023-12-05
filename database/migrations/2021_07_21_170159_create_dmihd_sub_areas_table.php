<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmihdSubAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmihd_sub_areas', function (Blueprint $table) {
            $table->id();
            $table->string('sub_area',255);
            $table->unsignedBigInteger('dmihd_area_id');
            $table->foreign('dmihd_area_id')->references('id')->on('dmihd_areas')->onDelete('cascade');
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
        Schema::dropIfExists('dmihd_sub_areas');
    }
}
