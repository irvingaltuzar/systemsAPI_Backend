<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiAccgInterimPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmi_accg_interim_payments', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->integer('accounting_company_id');
			$table->foreign('accounting_company_id')->references('id')->on('dmiaccg_accounting_companies');
			$table->date('diot_date');
			$table->string('diot_id_transaction_receipt', 25);
			$table->date('dyp_date');
			$table->string('dyp_id_transaction_receipt', 25);
            $table->boolean('is_yearly')->default(0);
			$table->string('comments', 100)->nullable();
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
        Schema::dropIfExists('dmi_accg_interim_payments');
    }
}
