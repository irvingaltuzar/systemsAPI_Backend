<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmihdAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmihd_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmihd_location_id');
            $table->foreign('dmihd_location_id')->references('id')->on('dmihd_locations')->onDelete('cascade');
            $table->string('area',255);
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
        Schema::dropIfExists('dmihd_areas');
    }
}
