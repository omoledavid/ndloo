<?php

namespace Database\Seeders;

use App\Models\BoostPlan;
use Illuminate\Database\Seeder;

class BoostPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            ['period' => 1, 'price' => 2],
            ['period' => 3, 'price' => 5],
            ['period' => 7, 'price' => 12],
            ['period' => 30, 'price' => 45],
        ];

        foreach ($plans as $plan) {
            BoostPlan::create($plan);
        }
    }
}
