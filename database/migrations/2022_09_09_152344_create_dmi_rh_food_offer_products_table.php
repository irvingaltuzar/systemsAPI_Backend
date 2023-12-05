<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiRhFoodOfferProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_food_offer_products', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->integer('food_order_product_id');
			$table->foreign('food_order_product_id')->references('id')->on('dmirh_food_order_products');
			$table->integer('user_buyer_id')->nullable();
			$table->foreign('user_buyer_id')->references('usuarioId')->on('seg_usuarios');
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
        Schema::dropIfExists('dmirh_food_offer_products');
    }
}
