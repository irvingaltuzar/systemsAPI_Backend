<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiRhDiningMenuPdfsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_dining_menu_pdfs', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('food_menu_id');
			$table->foreign('food_menu_id')->references('id')->on('food_menus');
            $table->string('file', 50);
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
        Schema::dropIfExists('dmirh_dining_menu_pdfs');
    }
}
