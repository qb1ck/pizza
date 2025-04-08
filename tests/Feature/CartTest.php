<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use DatabaseTransactions;

    protected function authenticate(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_can_add_product_to_cart()
    {
        $this->authenticate();

        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 500,
        ]);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 500,
        ]);

        $response->assertOk()
            ->assertJson(['message' => 'Продукт добавлен в корзину']);
    }

    public function test_add_product_exceeding_limit_returns_error()
    {
        $this->authenticate();

        $category = Category::find(1); // пиццы
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 500,
        ]);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 15, // превышает лимит
            'price' => 500,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Превышен лимит для категории «Пицца»: максимум 10.',
            ]);
    }

    public function test_can_remove_product_from_cart()
    {
        $this->authenticate();

        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 500,
        ]);

        // сначала добавим товар
        $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => 500,
        ]);

        // теперь удалим
        $response = $this->postJson('/api/cart/remove', [
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => 500,
        ]);

        $response->assertOk()
            ->assertJson(['message' => 'Продукт удален из корзины']);
    }

    public function test_get_products_from_current_cart()
    {
        $this->authenticate();

        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
        ]);

        $response = $this->getJson('/api/cart');

        $response->assertOk()
            ->assertJsonFragment(['name' => $product->name]);
    }
}
