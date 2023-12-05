<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmihdSubTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmihd_sub_tickets', function (Blueprint $table) {
            $table->id();
            $table->text('description');
            $table->unsignedBigInteger('dmihd_ticket_id');
            $table->foreign('dmihd_ticket_id')->references('id')->on('dmihd_tickets')->onDelete('cascade');
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
        Schema::dropIfExists('dmihd_sub_tickets');
    }
}
