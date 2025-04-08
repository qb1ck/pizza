<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(public OrderService $service) {}

    public function getOrders(): JsonResponse
    {
        $orders = auth()->user()->orders()
            ->with(['cart.products', 'status'])
            ->get();
        return response()->json(OrderResource::collection($orders));
    }

    public function createOrder(OrderRequest $request): JsonResponse
    {
        $cart = $this->service->getCart();

        if (!$cart || $cart->productsWithQuantity->isEmpty()) {
            return response()->json(['message' => 'Корзина пуста или не найдена.'], 400);
        }

        try {
            $order = $this->service->createOrderFromCart($cart, $request->validated());
            return response()->json(OrderResource::make($order));
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteOrder(): JsonResponse
    {
        $order = auth()->user()->orders()->where('status_id', 1)->first();

        if (!$order) {
            return response()->json(['message' => 'Заказ не найден'], 404);
        }

        $order->delete();
        return response()->json(['message' => 'Заказ удален']);
    }

    public function changeOrderStatus(): JsonResponse
    {
        $order = auth()->user()->orders()
            ->where('status_id', 1)
            ->with(['cart.products', 'status'])
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Заказ не найден'], 404);
        }

        $order = $this->service->changeOrderStatus($order);
        return response()->json(OrderResource::make($order));
    }
}
