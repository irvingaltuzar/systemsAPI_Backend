<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityIdDmihdTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dmihd_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('dmihd_priority_sub_area_id')->after('dmihd_ticket_statuses_id');
            $table->foreign('dmihd_priority_sub_area_id')->references('id')->on('dmihd_priority_sub_areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dmihd_tickets', function (Blueprint $table) {
            $table->dropForeign('dmihd_status_dmihd_priority_sub_area_id_foreign');
            $table->dropColumn('dmihd_priority_sub_area_id');
        });
    }
}
