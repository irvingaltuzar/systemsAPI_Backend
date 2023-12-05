<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmicotizaStagePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicotiza_stage_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmicotiza_department_id');
            $table->foreign('dmicotiza_department_id')->references('id')->on('dmicotiza_departments')->onDelete('cascade');
            $table->integer('stage');
            $table->double('price');
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
        Schema::dropIfExists('dmicotiza_stage_prices');
    }
}
