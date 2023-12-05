<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDmicotizaSubdivisionToDmicotizaDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dmicotiza_departments', function (Blueprint $table) {
            $table->unsignedBigInteger('dmicotiza_subdivision_id')->after('status');
            $table->foreign('dmicotiza_subdivision_id')->references('id')->on('dmicotiza_subdivisions')->onDelete('cascade');
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
            $table->dropForeign('dmicotiza_departments_dmicotiza_subdivision_id_foreign');
            $table->dropColumn('dmicotiza_subdivision_id');
        });
    }
}
