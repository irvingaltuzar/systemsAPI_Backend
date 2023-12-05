<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmihdValueFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmihd_value_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmihd_form_id');
            $table->foreign('dmihd_form_id')->references('id')->on('dmihd_forms')->onDelete('cascade');
            $table->unsignedBigInteger('dmihd_ticket_id');
            $table->foreign('dmihd_ticket_id')->references('id')->on('dmihd_tickets')->onDelete('cascade');
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
        Schema::dropIfExists('dmihd_value_forms');
    }
}
