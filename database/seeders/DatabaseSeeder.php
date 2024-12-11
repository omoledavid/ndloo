<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            CurrencySeeder::class,
            LanguageSeeder::class,
            UserSeeder::class,
            ProfileInfoSeeder::class,
            ProfileImageSeeder::class,
            BoostPlanSeeder::class,
            GiftPlanSeeder::class,
            MessageSeeder::class,
            ReactionSeeder::class,
            SubscriptionPlanSeeder::class,
            TransactionSeeder::class,
            ReportSeeder::class,
            MeetingSeeder::class,

        ]);
    }
}
