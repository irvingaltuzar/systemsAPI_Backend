<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhatssappMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('message_id',255);
            $table->string('recipient_phone',30);
            $table->string('type',30);
            $table->string('text_body',255)->nullable();
            $table->string('template_name',100)->nullable();
            $table->string('template_language_code',10)->nullable();
            $table->string('document_url',255)->nullable();
            $table->string('document_filename',255)->nullable();
            $table->string('document_id',255)->nullable();
            $table->string('document_content_type',255)->nullable();
            $table->timestamp('message_sent')->nullable();
            $table->timestamp('message_delivered')->nullable();
            $table->timestamp('message_read')->nullable();
            $table->string('messaging_product',30)->default('whatsapp');
            $table->string('recipient_type',30)->default('individual');
            $table->string('owner',100)->default('bot');
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
        Schema::dropIfExists('whatssapp_messages');
    }
}
