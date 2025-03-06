<?php

namespace App\Http\Controllers\api\Product;

use App\Enum\Product\ProductStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function productCategory(): JsonResponse
    {
        return response()->json(ProductCategory::query()->orderBy('sort_order')->get());
    }

    public function products(Request $request): AnonymousResourceCollection
    {
        $data = Product::query();

        if ($request->filled('search')) {
            $data = $data->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $data = $data->where('category_id', $request->category_id);
        }

        return ProductResource::collection($data->where('status', ProductStatusEnum::ACTIVE)->orderBy('created_at', 'desc')->paginate(20));

    }
}
