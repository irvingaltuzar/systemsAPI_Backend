<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDmicotizaClassificationIdToDmicotizaTypeDeparmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dmicotiza_type_departments', function (Blueprint $table) {
            $table->unsignedBigInteger('dmicotiza_classification_id')->after('type');
            $table->foreign('dmicotiza_classification_id')->references('id')->on('dmicotiza_classifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dmicotiza_type_departments', function (Blueprint $table) {
            $table->dropForeign('dmicotiza_type_departments_dmicotiza_classification_id_foreign');
            $table->dropColumn('dmicotiza_classification_id');
        });
    }
}
