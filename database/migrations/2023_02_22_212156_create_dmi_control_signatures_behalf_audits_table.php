<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiControlSignaturesBehalfAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicontrol_signatures_behalves_audit', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('origin_record_id')->index();
            $table->integer('signature_order')->index();
            $table->integer('seg_seccion_id')->index();
            $table->string('sign_behalf_usuario_ad')->index();
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
        Schema::dropIfExists('dmicontrol_signatures_behalves_audit');
    }
}
