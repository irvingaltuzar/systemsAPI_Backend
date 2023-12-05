<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiAccgFiscalPreclosingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmiaccg_fiscal_preclosing', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
			$table->integer('dmi_accounting_companies_id');
			$table->foreign('dmi_accounting_companies_id')->references('id')->on('dmi_accounting_companies');
			$table->string('month',3)->index();
			$table->string('year',4)->index();
			$table->decimal('accounting_utility',20,2)->nullable(true);
			$table->decimal('tax_utility',20,2)->nullable(true);
			$table->text("comments")->nullable(true);
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
        Schema::dropIfExists('dmiaccg_fiscal_preclosing');
    }
}
