<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmicotizaSubdivisionGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicotiza_subdivision_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group',255)->nullable();
            $table->integer('column');
            $table->unsignedBigInteger('dmicotiza_subdivision_id');
            $table->foreign('dmicotiza_subdivision_id')->references('id')->on('dmicotiza_subdivisions')->onDelete('cascade');
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
        Schema::dropIfExists('dmicotiza_subdivision_groups');
    }
}
