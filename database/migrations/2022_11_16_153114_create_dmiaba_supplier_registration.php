<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmiabaSupplierRegistration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmiaba_supplier_registration', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string("rfc");
            $table->string("business_name");
            $table->string("email");
            $table->string("phone");
            $table->string("status")->nullable();
            $table->string("type_person");
            $table->string("contact");
            $table->string("type_supplier")->nullable();
            $table->string("efo")->nullable();
            $table->date("date")->nullable();
            $table->string("status_files");
            $table->string("user");
            $table->string("bank");
            $table->string("bank_account");
            $table->string("bank_clabe");
            $table->string("bank_swift");
            $table->string("zip")->nullable();
            $table->string("address");
            $table->string("suburb");
            $table->string("city");
            $table->string("state");
            $table->string("cp");
            $table->string("country");
            $table->string("ban_email")->nullable();
            $table->string("referencia_intelisis")->nullable();
            $table->string("classification")->nullable();
            $table->string("web_page")->nullable();
            $table->string("credit_days");
            $table->string("currency");
            $table->string("motive_down")->nullable();
            $table->integer("update_user")->nullable();
            $table->string("user_approved")->nullable();
            $table->string("import")->nullable();
            $table->integer("manual_down")->nullable();
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
        Schema::dropIfExists('dmiaba_supplier_registration');
    }
}
