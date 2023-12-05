<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmicotizaProjectViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicotiza_project_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmicotiza_project_id');
            $table->foreign('dmicotiza_project_id')->references('id')->on('dmicotiza_projects')->onDelete('cascade');
            $table->string('view',255);
            $table->integer('position')->default(0);
            $table->integer('subdivision')->default(0);
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
        Schema::dropIfExists('dmicotiza_project_views');
    }
}
