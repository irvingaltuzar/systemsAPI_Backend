<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodMenuFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_food_menu_files', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->integer('food_menu_id');
			$table->foreign('food_menu_id')->references('id')->on('dmirh_food_menus');
            $table->string('file_menu', 50);
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
        Schema::dropIfExists('dmirh_food_menu_files');
    }
}
