<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmicontrolUserSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicontrol_user_settings', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('module',255)->index();
			$table->string('key',255)->index();
			$table->string('value',255)->index();
			$table->string('data',255)->nullable();
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
        Schema::dropIfExists('dmicontrol_user_settings');
    }
}
