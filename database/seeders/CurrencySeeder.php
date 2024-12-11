<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'British Pound Sterling',
                'abbr' => 'GBP',
            ],
            [
                'name' => 'Canadian Dollar',
                'abbr' => 'CAD',
            ],
            [
                'name' => 'Central African CFA Franc',
                'abbr' => 'XAF',
            ],
            [
                'name' => 'Chilean Peso',
                'abbr' => 'CLP',
            ],
            [
                'name' => 'Central African CFA Franc',
                'abbr' => 'XAF',
            ],

            [
                'name' => 'Colombian Peso',
                'abbr' => 'COP',
            ],
            [
                'name' => 'Egyptian Pound',
                'abbr' => 'EGP',
            ],
            [
                'name' => 'Euro',
                'abbr' => 'EUR',
            ],
            [
                'name' => 'Ghanaian Cedi',
                'abbr' => 'GHS',
            ],
            [
                'name' => 'Guinean Franc',
                'abbr' => 'GNF',
            ],
            [
                'name' => 'Kenyan shillings',
                'abbr' => 'KES',
            ],
            [
                'name' => 'Malawian Kwacha',
                'abbr' => 'MWK',
            ],
            [
                'name' => 'Moroccan Dirham',
                'abbr' => 'MAD',
            ],
            [
                'name' => 'Naira',
                'abbr' => 'NGN',
            ],
            [
                'name' => 'Rwandan Franc',
                'abbr' => 'RWF',
            ],

            [
                'name' => 'Sierra Leonean Leone',
                'abbr' => 'SLL',
            ],
            [
                'name' => 'South African Rand',
                'abbr' => 'ZAR',
            ],
            [
                'name' => 'Ugandan Shilling',
                'abbr' => 'UGX',
            ],

            [
                'name' => 'US Dollars',
                'abbr' => 'USD',
            ],

            [
                'name' => 'West African CFA Franc BCEAO',
                'abbr' => 'XOF',
            ],
            [
                'name' => 'Zambian Kwacha',
                'abbr' => 'ZMW',
            ],

        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
