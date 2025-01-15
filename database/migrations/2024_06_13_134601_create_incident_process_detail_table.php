<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentprocessDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_incident_process_detail', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('dmirh_incident_process_id');
            $table->integer('dmirh_personal_justification_id')->nullable();
            $table->foreign('dmirh_incident_process_id')->references('id')->on('dmirh_incident_process');
            $table->string('rfc',50);
            $table->string('name',100);
            $table->string('payment_period',50);
            $table->string('horario_base',50);
            $table->string('entry_hour',50);
            $table->date('date_incident');
            $table->string('status',50);
            $table->string('check_registers',255)->nullable();
            $table->string('discard_reason',255)->nullable();
            $table->string('observations',255)->nullable();
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
        Schema::dropIfExists('incident_process_detail');
    }
}
