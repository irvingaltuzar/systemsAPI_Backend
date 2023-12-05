<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeparmentToPersonalIntelisisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personal_intelisis', function (Blueprint $table) {
            $table->string('deparment')->after('position_company')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personal_intelisis', function (Blueprint $table) {
            $table->dropColumn('deparment');
        });
    }
}
