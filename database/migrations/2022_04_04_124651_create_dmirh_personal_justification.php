<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmirhPersonalJustification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_personal_justification', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->unsignedInteger('type_id');
            $table->foreign('type_id')->references('id')->on('cat_type_justification')->onDelete('cascade');
            $table->string('description',150);
            $table->string('file',150);
            $table->datetime('date');
            $table->string('user',50);
            $table->string('approved_by',50);
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
        Schema::dropIfExists('dmirh_personal_justification');
    }
}
