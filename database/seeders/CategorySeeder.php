<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Server & Cloud',
                'code' => 'SRV',
            ],
            [
                'id' => 2,
                'name' => 'Internet of Things',
                'code' => 'IOT',
            ],
            [
                'id' => 3,
                'name' => 'Multimedia & Drone',
                'code' => 'DRN',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['id' => $cat['id']], $cat);
        }
    }
}
