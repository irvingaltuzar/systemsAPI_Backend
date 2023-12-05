<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationCenter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_center', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('usuario_ad');
            $table->string('title');
            $table->string('message');
            $table->boolean('read_status')->default(0);
            $table->string('type');
            $table->string('link')->nullable();;
            $table->string('icon')->nullable();;
            $table->string('priority')->nullable();;
            $table->boolean('archived')->default(0);
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
        Schema::dropIfExists('notification_center');
    }
}
