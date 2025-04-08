<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Http\Resources\CartResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = $request->get('q');
        $categoriesQuery = Category::query();

        if ($q) {
            $foundedCategories = Category::query()->where('name', 'LIKE', "%{$q}%")->first();
            if ($foundedCategories) {
                $categoriesQuery->where('name', 'LIKE', "%{$q}%")->with('products');
            } else {
                $categoriesQuery->with(['products' => function ($productsQuery) use ($q) {
                    $productsQuery->where('name', 'LIKE', "%{$q}%");
                }]);
            }
        }

        return response()->json(CategoryResource::collection($categoriesQuery->get()->filter(function ($category) {
            return $category->products->count() > 0;
        })));
    }
}
