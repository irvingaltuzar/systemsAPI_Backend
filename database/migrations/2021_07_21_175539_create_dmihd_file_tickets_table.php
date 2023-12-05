<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmihdFileTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmihd_file_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmihd_ticket_id');
            $table->foreign('dmihd_ticket_id')->references('id')->on('dmihd_tickets')->onDelete('cascade');
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
        Schema::dropIfExists('dmihd_file_tickets');
    }
}
