<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiaccgAntiMoneyLaunderingLaw extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmiaccg_anti_money_laundering_law', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->Integer('dmiaccg_company_id');
            $table->foreign('dmiaccg_company_id')->references('id')->on('dmiaccg_accounting_companies')->onDelete('cascade');
            $table->string("month");
            $table->string("type");
            $table->string("no_folio");
            $table->date("date_send");
            $table->string("status_send");
            $table->string("person_object_send");
            $table->decimal('amount', 8, 2)->unsigned()->default(0);
            $table->string("full_expedient");
            $table->string("vulnerable_activity");
            // $table->date("date");
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
        Schema::dropIfExists('dmiaccg_anti_money_laundering_law');
    }
}
