<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmicotizaProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicotiza_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project',255)->unique();
            $table->double('hitch');
            $table->string('logo',255);
            $table->integer('level')->default(0);
            $table->boolean('low_level')->default(0);
            $table->boolean('hidden_subdivision')->default(0);
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
        Schema::dropIfExists('dmicotiza_projects');
    }
}
