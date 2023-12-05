<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiRhPaymentsLogsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_payments_logs_details', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->integer('payment_log_id');
			$table->foreign('payment_log_id')->references('id')->on('dmi_rh_payments_logs');
            $table->bigInteger('personal_intelisis_id')->unsigned();
			$table->foreign('personal_intelisis_id')->references('id')->on('personal_intelisis');
			$table->integer('food_order_id');
			$table->foreign('food_order_id')->references('id')->on('food_orders');
			$table->decimal('amount', 20, 2);
			$table->integer('products');
			$table->integer('salary_paid');
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
        Schema::dropIfExists('dmirh_payments_logs_details');
    }
}
