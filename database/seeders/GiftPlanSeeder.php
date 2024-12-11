<?php

namespace Database\Seeders;

use App\Models\GiftPlan;
use Illuminate\Database\Seeder;

class GiftPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            ['amount' => 5],
            ['amount' => 10],
            ['amount' => 20],
            ['amount' => 50],
            ['amount' => 100],
            ['amount' => 200],
            ['amount' => 500],
        ];

        foreach ($plans as $plan) {
            GiftPlan::create($plan);
        }
    }
}
