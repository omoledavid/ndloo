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
            $table->decimal('goal_progress', 10, 2)->nullable()->after('goal_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livestreams', function (Blueprint $table) {
            $table->dropColumn('goal_progress');
        });
    }
};
