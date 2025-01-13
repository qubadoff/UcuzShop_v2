<?php

namespace App\Http\Controllers\api\Cart;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CartController extends Controller
{
    public function cartList(): AnonymousResourceCollection
    {
        $data = Cart::query()->where('customer_id', auth()->user()->id)->get();

        return CartResource::collection($data);
    }

    public function addCart(Request $request)
    {
        $request->validate([
           'customer_id' => 'required|exists:customers,id',
           'product_id' => 'required|exists:products,id',
           'count' => 'required|numeric',
        ]);

        $cart = Cart::query()->where('customer_id', $request->customer_id)->where('product_id', $request->product_id)->first();

        if ($cart) {
            $cart->count += $request->count;
        } else {
            $cart = new Cart();
            $cart->customer_id = $request->customer_id;
            $cart->product_id = $request->product_id;
            $cart->count = $request->count;
        }
        $cart->save();

        return new CartResource($cart);
    }
}
