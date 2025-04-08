<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartProductRequest;
use App\Http\Resources\CartProductResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{

    public function __construct(public CartService $service)
    {
    }

    public function getProductFromCurrentCart(): JsonResponse
    {
        try {
            $cart = $this->service->getCurrentCart()->load('products.category');
            $products = $cart->products;

            return response()->json(CartProductResource::collection($products));
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Не удалось получить корзину'], 500);
        }
    }

    public function addProductToCart(CartProductRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $this->service->addToCart($data);
            return response()->json(['message' => 'Продукт добавлен в корзину']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function removeProductFromCart(CartProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->service->removeFromCart($data);

        return response()->json(['message' => 'Продукт удален из корзины']);
    }
}
