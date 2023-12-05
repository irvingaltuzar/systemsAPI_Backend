<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountingCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmiaccg_accounting_companies', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
            $table->integer('cat_work_station_id');
			$table->foreign('cat_work_station_id')->references('id')->on('cat_work_stations');
            $table->integer('cat_erp_id');
			$table->foreign('cat_erp_id')->references('id')->on('cat_erps');
            $table->string('business_name', 250);
            $table->bigInteger('manager_id')->unsigned();
			$table->foreign('manager_id')->references('id')->on('personal_intelisis');
            $table->bigInteger('accountant_id')->unsigned();
			$table->foreign('accountant_id')->references('id')->on('personal_intelisis');
            $table->boolean('has_law')->default(0);
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
        Schema::dropIfExists('dmiaccg_accounting_companies');
    }
}
