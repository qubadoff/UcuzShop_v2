<?php

namespace App\Http\Controllers\api\Cart;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartResource;
use App\Models\Cart;
use App\Notifications\AddCartNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CartController extends Controller
{
    public function cartList(): AnonymousResourceCollection
    {
        $data = Cart::query()->where('customer_id', auth()->user()->id)->get();

        return CartResource::collection($data);
    }

    public function addCart(Request $request): CartResource
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'count' => 'required|numeric|min:1',
        ]);

        $userId = auth()->user()->id;

        $cart = Cart::query()
            ->where('customer_id', $userId)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {
            $cart->count += $request->count;
        } else {
            $cart = new Cart();
            $cart->customer_id = $userId;
            $cart->product_id = $request->product_id;
            $cart->count = $request->count;
        }

        $cart->save();

        auth()->user()->notify(new AddCartNotification());

        return new CartResource($cart);
    }

    public function deleteCart(Request $request): JsonResponse
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
        ]);

        $cart = Cart::query()->find($request->cart_id);

        $cart->delete();

        return response()->json(['message' => 'Cart deleted successfully']);
    }

    public function updateCart(Request $request): CartResource
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'count' => 'required|numeric',
        ]);

        $cart = Cart::query()->find($request->cart_id);

        $cart->count = $request->count;
        $cart->save();


        return new CartResource($cart);
    }
}
