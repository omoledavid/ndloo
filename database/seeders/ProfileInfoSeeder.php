<?php

namespace Database\Seeders;

use App\Models\ProfileInfo;
use Illuminate\Database\Seeder;

class ProfileInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $infos = [
            //General
            [
                'category' => 'General',
                'name' => 'Work status',
                'type' => 'select',
                'options' => ['Unemployed', 'Self employed', 'Employed'],
            ],

            [
                'category' => 'General',
                'name' => 'Preferred language',
                'type' => 'input',
                'options' => [],
            ],

            [
                'category' => 'General',
                'name' => 'Education',
                'type' => 'select',
                'options' => ['College', 'Univerity', 'High school', 'None'],
            ],

            [
                'category' => 'General',
                'name' => 'Pets',
                'type' => 'select',
                'options' => ['Cats', 'Dog', 'Others', 'None'],
            ],

            //Appearance
            [
                'category' => 'Appearance',
                'name' => 'Height',
                'type' => 'input',
                'options' => [],
            ],
            [
                'category' => 'Appearance',
                'name' => 'Weight',
                'type' => 'input',
                'options' => [],
            ],
            [
                'category' => 'Appearance',
                'name' => 'Size',
                'type' => 'select',
                'options' => ['Slim', 'Chubby'],
            ],
            [
                'category' => 'Appearance',
                'name' => 'Skin color',
                'type' => 'select',
                'options' => ['White', 'Black'],
            ],
            [
                'category' => 'Appearance',
                'name' => 'Hair color',
                'type' => 'select',
                'options' => ['White', 'Black', 'Blond', 'Brunette'],
            ],

            //Personality
            [
                'category' => 'Personality',
                'name' => 'Zodiac sign',
                'type' => 'select',
                'options' => ['Aquarius', 'Libra', 'Taurus'],
            ],
            [
                'category' => 'Personality',
                'name' => 'Relationships',
                'type' => 'select',
                'options' => ['Introvert', 'Extrovert', 'Ambivert'],
            ],
            [
                'category' => 'Personality',
                'name' => 'Intelligence Quotient',
                'type' => 'select',
                'options' => ['Calm', 'Black'],
            ],
            [
                'category' => 'Personality',
                'name' => 'Emotional Quotient',
                'type' => 'select',
                'options' => ['Calm', 'Black'],
            ],

            //Lifestyle
            [
                'category' => 'Lifestyle',
                'name' => 'Religion',
                'type' => 'input',
                'options' => [],
            ],
            [
                'category' => 'Lifestyle',
                'name' => 'Habitat',
                'type' => 'select',
                'options' => ['Traveller', 'Squatting', 'Renting', 'Owns home'],
            ],
            [
                'category' => 'Lifestyle',
                'name' => 'Smoking',
                'type' => 'select',
                'options' => ['Yes', 'No'],
            ],
            [
                'category' => 'Lifestyle',
                'name' => 'Social lifestyle',
                'type' => 'select',
                'options' => ['Sociable', 'Stay at home'],
            ],
        ];

        foreach ($infos as $info) {
            ProfileInfo::create($info);
        }
    }
}
