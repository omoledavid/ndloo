<?php

namespace Database\Seeders;

use App\Models\ProfileImage;
use Illuminate\Database\Seeder;

class ProfileImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProfileImage::factory()
            ->times(5)
            ->create();
    }
}
