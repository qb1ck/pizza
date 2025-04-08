<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function getCart(): ?Cart
    {
        return Cart::where('user_id', auth()->id())
            ->where('open', 1)
            ->with(['user', 'products'])
            ->first();
    }

    public function getTotalPrice(Cart $cart): float|int
    {
        try {
            $total = 0;
            foreach ($cart->products as $product) {
                $total += $product->price * $product->pivot->quantity;
            }
            return $total;
        } catch (\Throwable $e) {
            report($e);
            throw new \Exception("Ошибка при расчёте стоимости заказа.");
        }
    }

    public function createOrderFromCart(Cart $cart, array $data): Order
    {
        return DB::transaction(function () use ($cart, $data) {
            $order = Order::create([
                'user_id' => $cart->user_id,
                'cart_id' => $cart->id,
                'total_price' => $this->getTotalPrice($cart),
                'status_id' => 1,
                'email' => $data['email'] ?? $cart->user->email,
                'phone' => $data['phone'] ?? $cart->user->phone,
                'address' => $data['address'] ?? $cart->user->address,
                'created_at' => now(),
            ]);

            $cart->update(['open' => false]);

            return $order;
        });
    }

    public function changeOrderStatus(Order $order): Order
    {
        $order->update([
            'status_id' => 2,
            'time' => Carbon::now()->addHours(2),
        ]);

        return $order;
    }
}
