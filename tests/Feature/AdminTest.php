<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($this->admin);
    }

    public function test_admin_can_see_categories_with_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/admin/products');

        $response->assertOk()
            ->assertJsonFragment(['id' => $category->id]);
    }

    public function test_admin_can_create_product(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/admin/products', [
            'name' => 'Тестовый товар',
            'category_id' => $category->id,
            'price' => '100',
            'slug' => 'test-product',
        ]);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'Тестовый товар']);
    }

    public function test_admin_can_update_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->postJson("/api/admin/products/{$product->id}", [
            'name' => 'Обновлённый товар',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Обновлённый товар']);
    }

    public function test_admin_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/admin/products/{$product->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_see_all_orders(): void
    {
        $order = Order::factory()->create();

        $response = $this->getJson('/api/admin/orders');

        $response->assertOk()
            ->assertJsonFragment(['id' => $order->id]);
    }

    public function test_admin_can_change_order_status(): void
    {
        $order = Order::factory()->create(['status_id' => 1]);

        $response = $this->postJson("/api/admin/orders/{$order->id}/status", [
            'status_id' => 2,
        ]);

        $response->assertOk()
            ->assertJsonFragment(['status_id' => 2]);
    }

    public function test_admin_can_assign_admin_role(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->patchJson("/api/admin/users/{$user->id}/make-admin");

        $response->assertCreated();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_admin' => true,
        ]);
    }
}
