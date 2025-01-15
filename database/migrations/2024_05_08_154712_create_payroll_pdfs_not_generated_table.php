<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollPdfsNotGeneratedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_pdfs_not_generated', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('rid',20);
            $table->string('data_payroll',255);
            $table->string('rfc',25)->nullable();
            $table->smallInteger('attempts_send')->nullable();
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
        Schema::dropIfExists('payroll_pdfs_not_generated');
    }
}
