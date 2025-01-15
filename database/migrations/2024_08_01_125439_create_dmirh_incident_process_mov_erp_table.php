<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmirhIncidentProcessMovErpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_incident_process_mov_erp', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('dmirh_incident_process_id');
            $table->foreign('dmirh_incident_process_id')->references('id')->on('dmirh_incident_process');
            $table->integer("mov_intelisis")->nullable();
            $table->string("insert_mov_erp",1000);
            $table->string("observations",350)->nullable();
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
        Schema::dropIfExists('dmirh_incident_process_mov_erp');
    }
}
