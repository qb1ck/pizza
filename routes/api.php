<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

    // 🔹 Каталог
Route::get('/products', [CatalogController::class, 'index']);

    // 🔹 Корзина
Route::middleware('auth:sanctum')->prefix('cart')->controller(CartController::class)->group(function () {
    Route::get('/', [CartController::class, 'getProductFromCurrentCart']);
    Route::post('/add', [CartController::class, 'addProductToCart']);
    Route::post('/remove', [CartController::class, 'removeProductFromCart']);
});


    // 🔹 Заказы
Route::middleware('auth:sanctum')->prefix('order')->group(function () {
    Route::get('/', [OrderController::class, 'getOrders']);
    Route::post('/create', [OrderController::class,'createOrder']);
    Route::post('/remove', [OrderController::class,'deleteOrder']);
    Route::post('/change-status', [OrderController::class,'changeOrderStatus']);
});


    // 🔹 Админка
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('admin')->group(function () {

    Route::patch('/users/{user}/make-admin', [AdminController::class, 'addToAdmin']);

    // 🔹 Продукты
    Route::get('/products', [AdminController::class, 'getProducts']);
    Route::post('/products', [AdminController::class, 'createProduct']);
    Route::post('/products/{product}', [AdminController::class, 'updateProduct']);
    Route::delete('/products/{product}', [AdminController::class, 'deleteProduct']);

    // 🔹 Заказы
    Route::get('/orders', [AdminController::class, 'getOrders']);
    Route::post('/orders/{order}/status', [AdminController::class, 'changeOrderStatus']);

});
