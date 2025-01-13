<?php

namespace App\Http\Controllers\api\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function sendOrder(Request $request): JsonResponse
    {
        $request->validate([
            'price' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $order = Order::query()->create([
            'customer_id' => auth()->user()->id,
            'price' => $request->price,
            'note' => $request->note
        ]);

        foreach ($request->products as $product) {
            $order->orderProduct()->create([
                'order_id' => $order->id,
                'product_id' => $product['id'],
                'count' => $product['count'],
            ]);
        }

        return response()->json([
            'message' => 'Order sent successfully',
        ]);
    }
}
