<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToDmicotizaDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dmicotiza_departments', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->unsignedBigInteger('dmicotiza_statu_id')->after('drawers');
            $table->foreign('dmicotiza_statu_id')->references('id')->on('dmicotiza_status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dmicotiza_departments', function (Blueprint $table) {
            $table->integer('status')->default(0)->after('drawers');
            $table->dropForeign('dmicotiza_departments_dmicotiza_statu_id_foreign');
            $table->dropColumn('dmicotiza_statu_id');
        });
    }
}
