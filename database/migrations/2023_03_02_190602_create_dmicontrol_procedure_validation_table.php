<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmicontrolProcedureValidationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicontrol_procedure_validation', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->string('seg_seccion_name')->index();
			$table->string('key')->index();
			$table->string('value')->index();
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
        Schema::dropIfExists('dmicontrol_procedure_validation');
    }
}
