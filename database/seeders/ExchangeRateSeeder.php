<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rates = [
            [
                'rate' => 0.77,
                'currency' => 'GBP',
            ],
            [
                'rate' => 1.37,
                'currency' => 'CAD',
            ],
            [
                'rate' => 599.75,
                'currency' => 'XAF',
            ],
            [
                'rate' => 925.93,
                'currency' => 'CLP',
            ],

            [
                'rate' => 3974.95,
                'currency' => 'COP',
            ],
            [
                'rate' => 48.20,
                'currency' => 'EGP',
            ],
            [
                'rate' => 0.91,
                'currency' => 'EUR',
            ],
            [
                'rate' => 15.41,
                'currency' => 'GHS',
            ],
            [
                'rate' => 8608.44,
                'currency' => 'GNF',
            ],
            [
                'rate' => 127.41,
                'currency' => 'KES',
            ],
            [
                'rate' => 1732.99,
                'currency' => 'MWK',
            ],
            [
                'rate' => 1732.99,
                'currency' => 'MAD',
            ],
            [
                'rate' => 1614.84,
                'currency' => 'NGN',
            ],
            [
                'rate' => 1308.61,
                'currency' => 'RWF',
            ],

            [
                'rate' => 18.19,
                'currency' => 'ZAR',
            ],
            [
                'rate' => 3697.08,
                'currency' => 'UGX',
            ],

            [
                'rate' => 1,
                'currency' => 'USD',
            ],

            [
                'rate' => 599.76,
                'currency' => 'XOF',
            ],
            [
                'rate' => 26.06,
                'currency' => 'ZMW',
            ],

        ];

        foreach ($rates as $rate) {
            ExchangeRate::create($rate);
        }
    }
}
