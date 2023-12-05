<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmicotizaStagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmicotiza_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmicotiza_classification_id');
            $table->foreign('dmicotiza_classification_id')->references('id')->on('dmicotiza_classifications')->onDelete('cascade');
            $table->unsignedBigInteger('dmicotiza_project_id');
            $table->foreign('dmicotiza_project_id')->references('id')->on('dmicotiza_projects')->onDelete('cascade');
            $table->integer('stage');
            $table->integer('quantity_department');
            $table->integer('quantity')->default(0);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('dmicotiza_stages');
    }
}
