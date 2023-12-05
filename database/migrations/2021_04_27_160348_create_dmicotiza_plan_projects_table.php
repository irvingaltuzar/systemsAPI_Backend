<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmicotizaPlanProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicotiza_plan_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmicotiza_plan_id');
            $table->foreign('dmicotiza_plan_id')->references('id')->on('dmicotiza_plans')->onDelete('cascade');

            $table->unsignedBigInteger('dmicotiza_project_id');
            $table->foreign('dmicotiza_project_id')->references('id')->on('dmicotiza_projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dmicotiza_plan_projects');
    }
}
