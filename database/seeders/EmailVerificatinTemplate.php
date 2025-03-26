<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailVerificatinTemplate extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NotificationTemplate::create([
            'act' => 'EVER_CODE',
            'name' => 'Verification - Email',
            'subj' => 'Please verify your email address',
            'email_body' => '
            <br />
            <div>
              <div style="font-family: Montserrat, sans-serif">Thanks For joining us.<br /></div>
              <div style="font-family: Montserrat, sans-serif">Please use the below code to verify your email address.<br /></div>
              <div style="font-family: Montserrat, sans-serif"><br /></div>
              <div style="font-family: Montserrat, sans-serif">
                Your email verification code is:<font size="6"><span style="font-weight: bolder">&nbsp;{{code}}</span></font>
              </div>
            </div>
            ',
            'sms_body' => '---',
            'push_notification_body' => null,
            'shortcodes' => '{"code":"Email verification code"}',
            'email_status' => 1,
            'sms_status' => 0,
            'push_notification_status' => 0,
        ]);
    }
}
