<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiAccountingMonthlyClosureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmiaccg_monthly_closure', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
			$table->integer('dmi_accounting_companies_id');
			$table->foreign('dmi_accounting_companies_id')->references('id')->on('dmi_accounting_companies');
			$table->string('month',3)->index();
			$table->string('year',4)->index();
			$table->date('date_accounting')->nullable(true);
			$table->string('file_accounting',255)->nullable(true);
			$table->date('date_fiscal')->nullable(true);
			$table->string('file_fiscal',255)->nullable(true);
			$table->date('date_payment')->nullable(true);
			$table->string('file_payment',255)->nullable(true);
			$table->text("observations")->nullable(true);
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
        Schema::dropIfExists('dmiaccg_monthly_closure');
    }
}
