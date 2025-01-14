<?php

namespace App\Http\Controllers\api\Product;

use App\Enum\Product\ProductStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function productCategory(): JsonResponse
    {
        return response()->json(ProductCategory::query()->orderBy('sort_order')->get());
    }

    public function products(): AnonymousResourceCollection
    {
        $data = Product::query()->where('status', ProductStatusEnum::ACTIVE)->orderBy('created_at', 'desc')->paginate(20);

        return ProductResource::collection($data);

    }
}
