<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditAutomatedProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_automated_processes', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('process_key',50);
            $table->string('process_name',255);
            $table->string('event',255);
            $table->string('affected',255)->nullable();
            $table->string('comments',2550)->nullable();
            $table->string('error',2550)->nullable();
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
        Schema::dropIfExists('audit_automated_processes');
    }
}
