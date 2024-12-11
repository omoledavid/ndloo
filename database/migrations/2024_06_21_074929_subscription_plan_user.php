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
        Schema::create('subscription_plan_user', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained();
            $table->foreignUuid('subscription_plan_id')->constrained();
            $table->boolean('active')->default(true);
            $table->timestamp('expires_on')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_user');
    }
};
