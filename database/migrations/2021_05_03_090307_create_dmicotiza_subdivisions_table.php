<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmicotizaSubdivisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicotiza_subdivisions', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->integer('position')->default(0);
            $table->unsignedBigInteger('dmicotiza_project_view_id');
            $table->foreign('dmicotiza_project_view_id')->references('id')->on('dmicotiza_project_views')->onDelete('cascade');
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
        Schema::dropIfExists('dmicotiza_subdivisions');
    }
}
