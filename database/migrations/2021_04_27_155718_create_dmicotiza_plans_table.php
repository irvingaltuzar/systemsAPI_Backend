<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmicotizaPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicotiza_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmicotiza_type_plan_id');
            $table->foreign('dmicotiza_type_plan_id')->references('id')->on('dmicotiza_type_plans')->onDelete('cascade');
            $table->string('plan',255);
            $table->integer('monthly_payment')->default(0)->nullable();
            $table->double('hitch_percentage')->default(0)->nullable();
            $table->double('interest_rate_percentage')->default(0)->nullable();
            $table->double('percentage_discount')->default(0)->nullable();
            $table->double('counter_delivery_months')->default(0)->nullable();
            $table->double('counter_delivery_percentage')->default(0)->nullable();
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
        Schema::dropIfExists('dmicotiza_plans');
    }
}
