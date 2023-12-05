<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmicontrolProcedureValidationGlossaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicontrol_procedure_validation_glossary', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->string('key');
			$table->text('description');
			$table->string('created_by');
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
        Schema::dropIfExists('dmicontrol_procedure_validation_glossary');
    }
}
