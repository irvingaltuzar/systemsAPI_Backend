<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiRhFoodOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_food_order_products', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->integer('food_order_id');
			$table->foreign('food_order_id')->references('id')->on('dmirh_food_orders');
			$table->integer('food_type_id');
			$table->foreign('food_type_id')->references('id')->on('cat_food_type');
			$table->integer('work_day_id');
			$table->foreign('work_day_id')->references('id')->on('cat_work_days');
            $table->boolean('offered')->default(0);
            $table->boolean('taked')->default(0);
            $table->boolean('bought')->default(0);
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
        Schema::dropIfExists('dmirh_food_order_products');
    }
}
