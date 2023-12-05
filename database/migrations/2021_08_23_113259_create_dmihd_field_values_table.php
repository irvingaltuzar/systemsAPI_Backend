<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmihdFieldValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmihd_field_values', function (Blueprint $table) {
            $table->id();
            $table->string('value',255);
            $table->unsignedBigInteger('dmihd_field_id');
            $table->foreign('dmihd_field_id')->references('id')->on('dmihd_fields')->onDelete('cascade');
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
        Schema::dropIfExists('dmihd_field_values');
    }
}
