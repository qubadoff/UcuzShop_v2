<?php

namespace App\Http\Controllers\api\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function sendOrder(Request $request): JsonResponse
    {
        $request->validate([
            'price' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $setting = Setting::query()->where('id', 1)->first()->min_order_price ? Setting::query()->where('id', 1)->first()->min_order_price : 30;

        if ($request->input('price') < $setting) {
            return response()->json([
                'message' => 'Sifarişin ümumi dəyəri 30 AZN - dən çox olmalıdır !',
            ], 422);
        }

        $customer = auth()->user();

        $order = Order::query()->create([
            'customer_id' => $customer->id,
            'price' => $request->price,
            'note' => $request->note
        ]);

        foreach ($request->products as $product) {
            $order->orderProduct()->create([
                'order_id' => $order->id,
                'product_id' => $product['product_id'],
                'count' => $product['count'],
            ]);
        }

        $customer->cart()->delete();

        return response()->json([
            'message' => 'Order sent successfully',
        ]);
    }

    public function orderDetails(): AnonymousResourceCollection
    {
        $customer = auth()->user()->id;

        $data = Order::query()->where('customer_id', $customer)->get();

        return OrderResource::collection($data);
    }
}
