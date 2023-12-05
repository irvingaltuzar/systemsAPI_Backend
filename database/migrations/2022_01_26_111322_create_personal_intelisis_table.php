<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalIntelisisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personal_intelisis', function (Blueprint $table) {
            $table->id();
            $table->string('personal_id');
            $table->string('name');
            $table->string('last_name');
            $table->date('birth')->nullable();
            $table->string('sex')->nullable();
            $table->string('email')->nullable();
            $table->string('extension')->nullable();
            $table->string('photo',255)->nullable();
            $table->string('position_company')->nullable();
            $table->date('date_admission')->nullable();
            $table->date('antiquity_date')->nullable();
            $table->string('location')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_code')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('plaza_id')->nullable();
            $table->string('top_plaza_id')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('personal_intelisis');
    }
}
