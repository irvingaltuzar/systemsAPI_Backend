<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcoreConfiguration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procore_configuration', function (Blueprint $table) {
            $table->id();
            $table->string('company_id', 1000);
            $table->string('client_id', 1000);
            $table->string('client_secret', 1000);
            $table->string('service_url', 1000);
            $table->string('service_url_login', 1000);
            $table->Integer('folder_copany_id');
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
        Schema::dropIfExists('procore_configuration');
    }
}
