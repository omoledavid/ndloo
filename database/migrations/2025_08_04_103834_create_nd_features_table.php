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
        Schema::create('nd_features', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'messages_per_day'
            $table->string('label')->nullable(); // Human-friendly label
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nd_features');
    }
};
