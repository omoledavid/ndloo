<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Fun',
                'description' => 'Entertaining and fun content',
            ],
            [
                'name' => 'Games',
                'description' => 'Gaming content and gameplay',
            ],
            [
                'name' => 'Lovetalk',
                'description' => 'Discussions about relationships and love',
            ],
            [
                'name' => 'Music',
                'description' => 'Music performances and discussions',
            ],
            [
                'name' => 'Art',
                'description' => 'Art creation and discussions',
            ],
        ];
        
        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }
    }
}