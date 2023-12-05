<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmicotizaDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicotiza_departments', function (Blueprint $table) {
            $table->id();
            $table->string('number',20);
            $table->unsignedBigInteger('dmicotiza_type_department_id');
            $table->foreign('dmicotiza_type_department_id')->references('id')->on('dmicotiza_type_departments')->onDelete('cascade');
            $table->integer('level');
            $table->float('m2');
            $table->double('price_m2');
            $table->unsignedBigInteger('dmicotiza_classification_id');
            $table->foreign('dmicotiza_classification_id')->references('id')->on('dmicotiza_classifications')->onDelete('cascade');
            $table->unsignedBigInteger('dmicotiza_project_id');
            $table->foreign('dmicotiza_project_id')->references('id')->on('dmicotiza_projects')->onDelete('cascade');
            $table->unsignedBigInteger('dmicotiza_project_view_id');
            $table->foreign('dmicotiza_project_view_id')->references('id')->on('dmicotiza_project_views')->onDelete('cascade');
            $table->integer('drawers')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('dmicotiza_departments');
    }
}
