<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_food_orders', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->integer('seg_usuario_id');
			$table->foreign('seg_usuario_id')->references('usuarioId')->on('seg_usuarios');
			$table->integer('food_menu_id');
			$table->foreign('food_menu_id')->references('id')->on('food_menus');
            $table->boolean('charged')->default(0);
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
        Schema::dropIfExists('dmirh_food_orders');
    }
}
