<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmihdTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmihd_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmihd_sub_area_id');
            $table->foreign('dmihd_sub_area_id')->references('id')->on('dmihd_sub_areas')->onDelete('cascade');
            $table->unsignedBigInteger('dmihd_status_id');
            $table->foreign('dmihd_status_id')->references('id')->on('dmihd_status')->onDelete('cascade');
            $table->unsignedBigInteger('dmihd_ticket_statuses_id');
            $table->foreign('dmihd_ticket_statuses_id')->references('id')->on('dmihd_ticket_statuses')->onDelete('cascade');
            $table->text('description');
            $table->string('subject',255);
            $table->unsignedBigInteger('user_created_id');
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
        Schema::dropIfExists('dmihd_tickets');
    }
}
