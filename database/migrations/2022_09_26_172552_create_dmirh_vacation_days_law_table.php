<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmirhVacationDaysLawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_vacation_days_law', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('vacation_type');
            $table->smallInteger("anniversary");
            $table->smallInteger("days");
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
        Schema::dropIfExists('dmirh_vacation_law');
    }
}
