<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getCurrentCart()
    {
        $userId = auth()->id();
        $cart = Cart::query()->where('user_id', $userId)->where('open', true)->first();

        if (!$cart) {
            $cart = Cart::query()->create(['user_id' => $userId, 'open' => true]);
        }
        return $cart;
    }

    public function addToCart(array $data): void
    {
        DB::transaction(function () use ($data) {
            $product = Product::with('category')->findOrFail($data['product_id']);
            $cart = $this->getCurrentCart();
            $existingProduct = $cart->products()->where('product_id', $product->id)->first();

            $categoryId = $product->category->id;
            $limits = config('cart.category_limits');
            $categoryNames = config('cart.category_names');
            $limit = $limits[$categoryId] ?? null;
            $categoryName = $categoryNames[$categoryId] ?? '';

            if ($existingProduct) {
                $newQuantity = $existingProduct->pivot->quantity + $data['quantity'];

                if ($limit !== null && $newQuantity > $limit) {
                    abort(400, "Превышен лимит для категории «{$categoryName}»: максимум {$limit}.");
                }

                $existingProduct->pivot->quantity = $newQuantity;

                if ($existingProduct->pivot->price != $data['price']) {
                    $existingProduct->pivot->price = $data['price'];
                }

                $existingProduct->pivot->save();
            } else {
                // Загружаем продукты с категориями, если товар новый
                $cart->loadMissing('products.category');

                $currentCategoryQuantity = $cart->products->sum(function ($item) use ($categoryId) {
                    return $item->category->id === $categoryId ? $item->pivot->quantity : 0;
                });

                $newQuantity = $currentCategoryQuantity + $data['quantity'];

                if ($limit !== null && $newQuantity > $limit) {
                    abort(400, "Превышен лимит для категории «{$categoryName}»: максимум {$limit}.");
                }

                $cart->products()->attach($data['product_id'], [
                    'quantity' => $data['quantity'],
                    'price' => $data['price'],
                ]);
            }
        });
    }

    public function removeFromCart(array $data): void
    {
        DB::transaction(function () use ($data) {
            $cart = $this->getCurrentCart();
            $existingProduct = $cart->products()->where('product_id', $data['product_id'])->first();

            if (!$existingProduct || $existingProduct->pivot->quantity - $data['quantity'] <= 0) {
                $cart->products()->detach($data['product_id']);
                return;
            }

            $existingProduct->pivot->quantity -= $data['quantity'];

            if ($existingProduct->pivot->price != $data['price']) {
                $existingProduct->pivot->price = $data['price'];
            }

            $existingProduct->pivot->save();
        });
    }
}
