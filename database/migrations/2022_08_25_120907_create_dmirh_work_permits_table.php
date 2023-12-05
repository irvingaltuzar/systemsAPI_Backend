<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmirhWorkPermitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_work_permits', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer("dmirh_type_permits_id");
            $table->integer("dmirh_permit_concepts_id")->nullable();
            $table->string("personal_intelisis_usuario_ad")->index();
            $table->string("personal_id",100);
            $table->date("start_date");
            $table->date("end_date");
            $table->date("return_date")->nullable()->index();
            $table->smallInteger("total_days");
            $table->date("date_request");
            $table->string("reason",255)->index();
            $table->text("comments");
            $table->string("document",255)->nullable();
            $table->integer("mov_intelisis")->nullable();;
            $table->string("status",100);
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
        Schema::dropIfExists('dmirh_work_permits');
    }
}
