<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('ruta')->nullable();
            $table->string('nombre');
            $table->string('apellidos')->nullable();
            $table->string('sexo')->nullable();
            $table->string('descripcion')->nullable();
            $table->string('num_oficina')->nullable();
            $table->string('num_telefono')->nullable();
            $table->string('roles')->nullable();
            $table->string('email');
            $table->string('usuario')->unique();
            $table->string('password');
            $table->string('foto_perfil')->nullable();
            $table->rememberToken();


            $table->timestamps();
        // });
        // Schema::create('users', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->string('username')->unique(); // was 'email'
        //     $table->string('password');
        //     $table->string('name'); // to be read from LDAP
        //     $table->string('phone'); // extra field to read from LDAP
        //     $table->rememberToken();
        //     $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}