<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmihdFileSubTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmihd_file_sub_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmihd_sub_ticket_id');
            $table->foreign('dmihd_sub_ticket_id')->references('id')->on('dmihd_sub_tickets')->onDelete('cascade');
            $table->unsignedBigInteger('dmihd_file_id');
            $table->foreign('dmihd_file_id')->references('id')->on('dmihd_files')->onDelete('cascade');
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
        Schema::dropIfExists('dmihd_file_sub_tickets');
    }
}
