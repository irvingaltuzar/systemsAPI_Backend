<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOauthRefreshTokenProcore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_refresh_token_procore', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('access_token_id', 1000);
            $table->string('refresh_token_id', 100);
            $table->string('token_type');
            $table->boolean('revoked')->nullable();
            $table->Integer('expires_at')->nullable();
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
        Schema::dropIfExists('oauth_refresh_token_procore');
    }
}
