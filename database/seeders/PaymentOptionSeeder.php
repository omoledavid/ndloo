<?php

namespace Database\Seeders;

use App\Models\PaymentOption;
use Illuminate\Database\Seeder;

class PaymentOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            [
                'name' => 'Flutterwave',
                'logo' => 'assets/img/flutterwave.png',
                'slug' => 'flutterwave',
            ],
            [
                'name' => 'Korapay',
                'logo' => 'assets/img/korapay.png',
                'slug' => 'korapay',
            ],
        ];

        foreach ($options as $option) {
            PaymentOption::create($option);
        }
    }
}
