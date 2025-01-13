<?php

namespace App\Http\Controllers\api\Cart;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartResource;
use App\Models\Cart;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CartController extends Controller
{
    public function cart(): AnonymousResourceCollection
    {
        $data = Cart::query()->where('customer_id', auth()->user()->id)->get();

        return CartResource::collection($data);
    }
}
