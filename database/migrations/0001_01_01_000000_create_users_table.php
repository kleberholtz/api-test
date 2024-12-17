<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id', 32)->primary();
            $table->string('name', 255);
            $table->string('email', 200)->unique();
            $table->string('phone', 32)->index()->nullable()->default(null);
            $table->string('password', 255);
            $table->string('avatar', 255)->nullable()->default(null);
            $table->json('permissions')->default('[]');
            $table->string('verified_at', 6)->nullable()->default(null);
            $table->bigInteger('ml_id', unsigned: true)->index()->nullable()->default(null);
            $table->json('data')->default('{}');
            $table->timestamp('created_at', 6)->useCurrent()->index();
            $table->timestamp('updated_at', 6)->useCurrent()->index();
        });

        Schema::create('access_tokens', function (Blueprint $table) {
            $table->string('token', 40)->primary();
            $table->string('user_id', 32)->index();
            $table->timestamp('expires_at', 6)->nullable()->default(null)->index();
            $table->string('name', 100)->nullable()->default(null)->fulltext();
            $table->json('data')->nullable()->default(null);
            $table->timestamp('created_at', 6)->useCurrent()->index();
            $table->timestamp('updated_at', 6)->useCurrent()->index();
        });

        Schema::table('access_tokens', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at', 6)->useCurrent()->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('access_tokens');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
