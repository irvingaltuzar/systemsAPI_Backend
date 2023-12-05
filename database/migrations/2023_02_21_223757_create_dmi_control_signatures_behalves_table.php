<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiControlSignaturesBehalvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicontrol_signatures_behalves', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
			$table->string("usuario_ad")->index();
			$table->string("behalf_usuario_ad")->index();
			$table->integer('order')->index();
			$table->integer('seg_seccion_id')->index();
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
        Schema::dropIfExists('dmicontrol_signatures_behalves');
    }
}
