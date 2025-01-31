<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GeneralSetting::create([
            'site_name' => "Ndloo",
            'cur_text' => "USD",
            'cur_sym' => "$",
            'email_from' => "info@ndloo.com",
            'email_template' => NULL,
            'sms_body' => null,
            'sms_from' => null,
            'mail_config' => '{"name":"php"}',
            'sms_config' => null,
            'global_shortcodes' => "Dloo",
            'ev' => 0,
            'en' => 1,
            'sv' => 0,
            'maintenance_mode' => 0
        ]);
    }
}
