<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiabaDocumentsSupplier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmiaba_documents_supplier', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string("name");
            $table->string("url");
            $table->Integer('cat_document_supplier_id');
            $table->foreign('cat_document_supplier_id')->references('id')->on('cat_document_supplier')->onDelete('cascade');
            $table->Integer('dmiaba_supplier_registration_id');
            $table->foreign('dmiaba_supplier_registration_id')->references('id')->on('dmiaba_supplier_registration')->onDelete('cascade');
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
        Schema::dropIfExists('dmiaba_documents_supplier');
    }
}
