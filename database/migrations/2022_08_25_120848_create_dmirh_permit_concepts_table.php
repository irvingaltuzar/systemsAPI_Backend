<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmirhPermitConceptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_permit_concepts', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer("dmirh_type_permits_id");
            $table->string("description",255);
            $table->smallInteger("days")->nullable();
            $table->foreign("dmirh_type_permits_id")->references("id")->on("dmirh_type_permits")->onDelete("cascade");
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
        Schema::dropIfExists('dmirh_permit_concepts');
    }
}
