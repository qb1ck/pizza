<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pizzas = Category::query()->where('name', 'Пиццы')->first();
        $drinks = Category::query()->where('name', 'Напитки')->first();

        $products = [
            ['name' => 'Маргарита', 'description' => 'Классическая пицца с томатами и сыром', 'price' => 500, 'category_id' => $pizzas->id],
            ['name' => 'Пепперони', 'description' => 'Пикантная пицца с пепперони', 'price' => 600, 'category_id' => $pizzas->id],
            ['name' => 'Гавайская', 'description' => 'Пицца с ананасами и курицей', 'price' => 550, 'category_id' => $pizzas->id],
            ['name' => 'Четыре сыра', 'description' => 'Пицца с миксом сыров', 'price' => 700, 'category_id' => $pizzas->id],
            ['name' => 'Кола', 'description' => 'Газированный напиток', 'price' => 150, 'category_id' => $drinks->id],
            ['name' => 'Фанта', 'description' => 'Апельсиновый газированный напиток', 'price' => 150, 'category_id' => $drinks->id],
            ['name' => 'Спрайт', 'description' => 'Лимонно-лаймовый напиток', 'price' => 150, 'category_id' => $drinks->id],
            ['name' => 'Морс', 'description' => 'Ягодный освежающий напиток', 'price' => 120, 'category_id' => $drinks->id],
        ];
        foreach ($products as $product) {
            Product::factory()->create($product);
        }
    }
}
