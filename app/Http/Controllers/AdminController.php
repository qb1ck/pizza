<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\AdminOrderResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function addToAdmin(User $user, AdminService $service): JsonResponse
    {
        $service->addAdmin($user);
        return response()->json(['message' => 'Пользователь назначен администратором'], 201);
    }

    public function getProducts(Request $request, AdminService $service): JsonResponse
    {
        $categories = $service->searchCategoriesWithProducts($request->get('q'));
        return response()->json(CategoryResource::collection($categories));
    }

    public function createProduct(ProductRequest $request, AdminService $service): JsonResponse
    {
        try {
            $product = $service->createProduct($request->validated());
            return response()->json(ProductResource::make($product), 201);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Не удалось создать продукт: ' . $e->getMessage()], 500);
        }
    }

    public function updateProduct(UpdateProductRequest $request, Product $product, AdminService $service): JsonResponse
    {
        try {
            $product = $service->updateProduct($product, $request->validated());
            return response()->json(ProductResource::make($product));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Не удалось обновить продукт: ' . $e->getMessage()], 500);
        }

    }

    public function deleteProduct(Product $product, AdminService $service): JsonResponse
    {
        try {
            $service->deleteProduct($product);
            return response()->json(['message' => 'Продукт успешно удалён']);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Не удалось удалить продукт' . $e->getMessage()], 500);
        }

    }

    public function getOrders(AdminService $service): JsonResponse
    {
        $orders = $service->getAllOrders();
        return response()->json(AdminOrderResource::collection($orders));
    }

    public function changeOrderStatus(OrderStatusRequest $request, Order $order, AdminService $service): JsonResponse
    {
        $order = $service->changeOrderStatus($order, $request->validated('status_id'));
        return response()->json(AdminOrderResource::make($order));
    }
}
