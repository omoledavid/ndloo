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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name', 40)->nullable();
            $table->string('cur_text',40)->nullable()->comment('currency text e.g USD');
            $table->string('cur_sym',40)->nullable()->comment('currency symbol e.g $');
            $table->string('email_from',40)->nullable();
            $table->text('email_template')->nullable();
            $table->string('sms_body',255)->nullable();
            $table->string('sms_from',255)->nullable();
            $table->text('mail_config')->nullable()->comment('mail configuration e.g mailtrap, phpmailer, smtp, etc');
            $table->text('sms_config')->nullable();
            $table->text('global_shortcodes')->nullable();
            $table->tinyInteger('ev')->default(0)->comment('email verification, 0 unverified 1 verified');
            $table->tinyInteger('en')->default(0)->comment('email notification, 0 disabled 1 enable');
            $table->tinyInteger('sv')->default(0)->comment('sms verification, 0 disabled 1 enable');
            $table->tinyInteger('maintenance_mode')->default(0)->comment('maintenance mode, 0 disabled 1 enable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
