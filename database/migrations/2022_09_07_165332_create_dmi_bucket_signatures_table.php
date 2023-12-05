<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiBucketSignaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmi_bucket_signatures', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('origin_record_id')->index();
            $table->integer('seg_seccion_id')->index();
            $table->string('personal_intelisis_usuario_ad')->index();
            $table->string('status')->nullable();
            $table->timestamp('signed_date')->nullable();
            $table->tinyInteger('order')->nullable();
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
        Schema::dropIfExists('dmi_bucket_signatures');
    }
}
