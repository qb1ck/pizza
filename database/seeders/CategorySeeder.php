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
        Category::factory()->create([
            'name' => 'Пиццы', 'description' => 'Вкусные пиццы на любой вкус', 'slug' => 'pizzas'
        ]);
        Category::factory()->create([
            'name' => 'Напитки', 'description' => 'Прохладительные напитки', 'slug' => 'drinks'
        ]);

    }
}
