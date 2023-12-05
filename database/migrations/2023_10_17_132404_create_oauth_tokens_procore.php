<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthTokensProcore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_tokens_procore', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->unsignedBigInteger('resource_owner_id')->nullable();
            $table->boolean('revoked')->nullable();
            $table->timestamps();
        });
    }

    /**               
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oauth_tokens_procore');
    }
}
