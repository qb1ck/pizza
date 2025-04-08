<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_create_order(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 500
        ]);

        $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 500
        ]);

        $response = $this->postJson('/api/order/create', [
            'email' => 'test@example.com',
            'phone' => '+79991234567',
            'address' => 'ул. Ленина, 1'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'products',
                'total_price',
                'address',
                'phone',
                'time',
                'status',
            ]);
    }
}
