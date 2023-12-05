<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmirhVacationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmirh_vacation', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string("personal_intelisis_usuario_ad",)->index();
			$table->string("personal_id",100);
            $table->string("period",100);
            $table->string("type_period",50);
			$table->date("start_date");
            $table->date("end_date");
            $table->date("return_date")->nullable()->index();
            $table->smallInteger("total_days");
            $table->smallInteger("previous_balance");
            $table->date("date_request");
            $table->string("document",255)->nullable();
            $table->integer("mov_intelisis")->nullable();;
            $table->string("status",100);
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
        Schema::dropIfExists('dmirh_vacation');
    }
}
