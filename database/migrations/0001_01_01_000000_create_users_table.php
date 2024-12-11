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
            $table->uuid('id')->primary();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('phone')->nullable();
            $table->integer('age')->default(18);
            $table->bigInteger('wallet')->default(0);
            $table->string('gender')->default('male');
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->timestamp('dob')->nullable();
            $table->string('language')->default('en');
            $table->foreignId('country_id')->constrained();
            $table->string('avatar')->default('storage/avatar/default.png');
            $table->string('password')->nullable();
            $table->string('token')->nullable();
            $table->integer('status')->default(0);
            $table->boolean('active')->default(false);
            $table->boolean('boosted')->default(false);
            $table->boolean('pushNotice')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email');
            $table->string('token');
            $table->timestamps();
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
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
