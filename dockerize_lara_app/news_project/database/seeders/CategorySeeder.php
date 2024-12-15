<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Laravel'],
            ['name' => 'Apple'],
            ['name' => 'Politics'],
            ['name' => 'Movies'],
            ['name' => 'Technology'],
            ['name' => 'AI'],
            ['name' => 'News'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
