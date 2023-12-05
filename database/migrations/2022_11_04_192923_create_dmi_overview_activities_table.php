<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiOverviewActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmiaccg_overview_activities', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->integer('accounting_company_id');
			$table->foreign('accounting_company_id')->references('id')->on('dmiaccg_accounting_companies');
			$table->integer('cat_overview_id');
			$table->foreign('cat_overview_id')->references('id')->on('cat_overviews');
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
        Schema::dropIfExists('dmiaccg_overview_activities');
    }
}
