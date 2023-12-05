<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmihdValueFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmihd_value_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmihd_value_form_id');
            $table->foreign('dmihd_value_form_id')->references('id')->on('dmihd_value_forms')->onDelete('cascade');
            $table->unsignedBigInteger('dmihd_field_id');
            $table->foreign('dmihd_field_id')->references('id')->on('dmihd_fields')->onDelete('cascade');
            $table->string('value',255);
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
        Schema::dropIfExists('dmihd_value_fields');
    }
}
