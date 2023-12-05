<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiRhPaymentsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_payments_logs', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->integer('seg_usuario_id');
			$table->foreign('seg_usuario_id')->references('usuarioId')->on('seg_usuarios');
			$table->date('start_date');
			$table->date('finish_date');
			$table->string('sp_token', 100);
			$table->integer('salary_paid');
			$table->string('account_code', 30);
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
        Schema::dropIfExists('dmirh_payments_logs');
    }
}
