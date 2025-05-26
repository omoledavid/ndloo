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
        Schema::table('livestreams', function (Blueprint $table) {
            $table->string('key_words')->nullable()->after('title');
            $table->string('ticket_amount')->nullable()->after('key_words');
            $table->string('goal_title')->nullable()->after('ticket_amount');
            $table->decimal('goal_amount', 10, 2)->nullable()->after('goal_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livestreams', function (Blueprint $table) {
            $table->dropColumn(['key_words', 'ticket_amount', 'goal_title', 'goal_amount']);
        });
    }
};
