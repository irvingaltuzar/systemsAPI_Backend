<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiControlPlazaSubstitutionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicontrol_plaza_substitution', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string("plaza_id")->index();
            $table->string("substitute_plaza_id")->index();
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
        Schema::dropIfExists('dmi_control_plaza_substitution');
    }
}
