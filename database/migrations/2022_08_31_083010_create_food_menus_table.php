<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_food_menus', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->bigInteger('location_id')->unsigned();
			$table->foreign('location_id')->references('id')->on('locations');
			$table->text('enabled_days');
			$table->decimal('employee_price', 8, 2)->unsigned()->default(0);
			$table->decimal('general_price', 8, 2)->unsigned()->default(0);
			$table->date('start_date');
			$table->date('finish_date');
            $table->boolean('is_open')->default(0);
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
        Schema::dropIfExists('dmirh_food_menus');
    }
}
