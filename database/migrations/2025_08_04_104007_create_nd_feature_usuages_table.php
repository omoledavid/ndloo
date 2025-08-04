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
        Schema::create('nd_feature_usuages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('nd_subscriptions')->cascadeOnDelete();
            $table->foreignId('feature_id')->constrained('nd_features')->cascadeOnDelete();
            $table->integer('used')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nd_feature_usuages');
    }
};
