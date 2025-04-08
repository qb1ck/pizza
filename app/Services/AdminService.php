<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminService
{
    public function addAdmin(User $user): void
    {
        $user->update(['is_admin' => true]);
    }

    public function searchCategoriesWithProducts(?string $query = null): Collection
    {
        $categoriesQuery = Category::with('products');

        if ($query) {
            $categoriesQuery->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhereHas('products', function ($productQuery) use ($query) {
                        $productQuery->where('name', 'LIKE', "%{$query}%");
                    });
            });
        }

        return $categoriesQuery->get();
    }

    public function createProduct(array $data): Product
    {
        return Product::create($data);
    }

    public function updateProduct(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    public function deleteProduct(Product $product): void
    {
        $product->delete();
    }

    public function getAllOrders(): Collection
    {
        return Order::with(['user', 'cart.products'])->orderByDesc('created_at')->get();
    }

    public function changeOrderStatus(Order $order, int $statusId): Order
    {
        $order->update(['status_id' => $statusId]);
        return $order;
    }
}
