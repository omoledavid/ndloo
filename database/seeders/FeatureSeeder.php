<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('nd_features')->insert([
            ['name' => 'message_count', 'label' => 'Message Count'],
            ['name' => 'calls_count', 'label' => 'Calls Count'],
            ['name' => 'super_like', 'label' => 'Super Like'],
            ['name' => 'instant_message', 'label' => 'Instant Message'],
            ['name' => 'rewind', 'label' => 'Rewind'],
            ['name' => 'can_chat', 'label' => 'Can Chat'],
            ['name' => 'can_read_messages', 'label' => 'Can Read Messages'],
            ['name' => 'can_redeem_gift', 'label' => 'Can Redeem Gift'],
            ['name' => 'profile_boost', 'label' => 'Profile Boost'],
            ['name' => 'likes', 'label' => 'Likes'],
            ['name' => 'can_see_likes', 'label' => 'Can See who likes my profile'],
        ]);
        DB::table('nd_plans')->insert([
            ['name' => 'Free', 'price' => 0, 'duration_days' => null],
            ['name' => 'Silver', 'price' => 5, 'duration_days' => 30],
            ['name' => 'Gold', 'price' => 10, 'duration_days' => 30],
            ['name' => 'Platinum', 'price' => 15, 'duration_days' => 30],
        ]);
        DB::table('nd_plan_features')->insert([
            ['plan_id' => 1, 'feature_id' => 1, 'limit' => 100],
            ['plan_id' => 1, 'feature_id' => 2, 'limit' => 100],
            ['plan_id' => 1, 'feature_id' => 3, 'limit' => 100],
            ['plan_id' => 1, 'feature_id' => 4, 'limit' => 100],
            ['plan_id' => 1, 'feature_id' => 5, 'limit' => 100],
            ['plan_id' => 1, 'feature_id' => 6, 'limit' => 100],
            ['plan_id' => 1, 'feature_id' => 7, 'limit' => 100],
            ['plan_id' => 2, 'feature_id' => 2, 'limit' => 100],
            ['plan_id' => 2, 'feature_id' => 3, 'limit' => 100],
            ['plan_id' => 2, 'feature_id' => 4, 'limit' => 100],
        ]);
    }
}
