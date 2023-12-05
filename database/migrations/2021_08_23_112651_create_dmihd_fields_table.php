<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmihdFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmihd_fields', function (Blueprint $table) {
            $table->id();
            $table->string('type',255);
            $table->string('label',255);
            $table->string('name',255);
            $table->boolean('required')->default(0);
            $table->unsignedBigInteger('dmihd_form_id');
            $table->foreign('dmihd_form_id')->references('id')->on('dmihd_forms')->onDelete('cascade');
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
        Schema::dropIfExists('dmihd_fields');
    }
}
