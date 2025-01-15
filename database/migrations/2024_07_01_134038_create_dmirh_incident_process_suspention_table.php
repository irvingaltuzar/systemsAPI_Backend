<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmirhIncidentProcessSuspentionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_incident_process_suspention', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('dmirh_incident_process_id');
            $table->foreign('dmirh_incident_process_id')->references('id')->on('dmirh_incident_process');
            $table->string('rfc',50);
            $table->string('calendar_month',2);
            $table->string('calendar_year',4);
            $table->integer('days_suspention');
            $table->integer('number_delays');
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
        Schema::dropIfExists('dmirh_incident_process_suspention');
    }
}
