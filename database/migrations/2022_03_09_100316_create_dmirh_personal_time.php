<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmirhPersonalTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_personal_time', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('user',50);
            $table->unsignedInteger('dmirh_cat_time_status_id');
            $table->foreign('dmirh_cat_time_status_id')->references('id')->on('cat_time_status')->onDelete('cascade');
            $table->datetime('start_date');
            $table->string('approved_by',30);
            $table->datetime('approved_date');
            $table->boolean('active')->default(0);
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dmirh_personal_time');
    }
}
