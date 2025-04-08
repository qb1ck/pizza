<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_see_categories_with_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'products' => [
                        '*' => ['id', 'name', 'description', 'price'],
                    ],
                ],
            ]);
    }
}
