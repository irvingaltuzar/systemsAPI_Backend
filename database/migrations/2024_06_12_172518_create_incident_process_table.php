<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_incident_process', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('locations',255);
            $table->string('payment_period',30);
            $table->string('status',30);
            $table->string('rfc_generated',50)->nullable();
            $table->string('observations',255)->nullable();
            $table->text('collaborators_contemplated_rfc')->nullable();
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
        Schema::dropIfExists('dmirh_incident_process');
    }
}
