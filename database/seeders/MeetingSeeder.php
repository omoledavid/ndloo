<?php

namespace Database\Seeders;

use App\Models\Meeting;
use Illuminate\Database\Seeder;

class MeetingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Meeting::factory()
            ->times(5)
            ->create();
    }
}
