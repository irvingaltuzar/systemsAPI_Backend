<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiaccgDiotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmiaccg_diots', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->integer('accounting_company_id');
			$table->foreign('accounting_company_id')->references('id')->on('dmiaccg_accounting_companies');
			$table->date('date');
			$table->string('comments', 40);
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
        Schema::dropIfExists('dmiaccg_diots');
    }
}
