<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'name' => 'English',
                'abbr' => 'en',
            ],
            [
                'name' => 'French',
                'abbr' => 'fr',
            ],
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
