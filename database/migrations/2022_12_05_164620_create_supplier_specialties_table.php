<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierSpecialtiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_specialties', function (Blueprint $table) {
			$table->integer('id')->autoIncrement();
			$table->integer('supplier_id');
			$table->foreign('supplier_id')->references('id')->on('dmiaba_supplier_registration');
			$table->integer('cat_supplier_specialty');
			$table->foreign('cat_supplier_specialty')->references('id')->on('cat_supplier_specialties');
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
        Schema::dropIfExists('supplier_specialties');
    }
}
