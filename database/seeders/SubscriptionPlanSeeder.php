<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [

            [
                'name' => 'Free',
                'desc' => '',
                'price' => 0,
                'chat' => true,
                'call' => false,
                'save_messages' => false,
                'hide_profile' => false,
            ],
            [
                'name' => 'Gold',
                'desc' => '',
                'price' => 15,
                'chat' => true,
                'call' => false,
                'save_messages' => true,
                'hide_profile' => true,
            ],
            [
                'name' => 'Premium',
                'desc' => '',
                'price' => 30,
                'chat' => true,
                'call' => true,
                'save_messages' => true,
                'hide_profile' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}
