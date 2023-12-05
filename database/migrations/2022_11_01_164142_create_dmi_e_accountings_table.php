<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiEAccountingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmiaccg_e_accountings', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->integer('accounting_company_id');
			$table->foreign('accounting_company_id')->references('id')->on('dmiaccg_accounting_companies');
			$table->integer('cat_e_accounting_status_id');
			$table->foreign('cat_e_accounting_status_id')->references('id')->on('cat_e_accounting_statuses');
			$table->date('date');
			$table->string('id_transaction_receipt', 25);
            $table->boolean('is_yearly')->default(0);
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
        Schema::dropIfExists('dmiaccg_e_accountings');
    }
}
