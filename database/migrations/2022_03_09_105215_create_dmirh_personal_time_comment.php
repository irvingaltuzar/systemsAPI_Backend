<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmirhPersonalTimeComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_personal_time_comment', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->unsignedInteger('dmirh_personal_time_id')->nullable();;
            $table->foreign('dmirh_personal_time_id')->references('id')->on('dmirh_personal_time')->onDelete('cascade');
            $table->string('comment',150);
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
        Schema::dropIfExists('dmirh_personal_time_comment');
    }
}
