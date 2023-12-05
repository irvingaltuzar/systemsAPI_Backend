<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiControlAuthorizationSignatures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicontrol_authorization_signatures', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('plaza_id',100);
            $table->integer('subsecId');
            $table->unsignedBigInteger('dmi_control_process_id');
            $table->foreign('dmi_control_process_id')->references('id')->on('dmi_control_process')->onDelete('cascade');
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
        Schema::dropIfExists('dmi_control_authorization_signatures');
    }
}
