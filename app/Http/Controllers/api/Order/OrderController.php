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
            'customer_id' => 'required|exists:customers,id',
            'price' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $order = new Order();
        $order->customer_id = $request->customer_id;
        $order->price = $request->price;
        $order->notes = $request->notes;
        $order->save();

        return response()->json([
            'message' => 'Order sent successfully',
        ]);
    }
}
