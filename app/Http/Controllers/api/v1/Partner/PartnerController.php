<?php

namespace App\Http\Controllers\api\v1\Partner;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Partner\PartnerResource;
use App\Models\Order;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class PartnerController extends Controller
{
    public function __construct(){}

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|numeric',
            'password' => 'required|string',
        ]);

        $user = Partner::query()->where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 422);
        }

        $token = $user->createToken('PartnerAuthToken')->plainTextToken;

        return response()->json([
            'token' => $token,
            'partner' => PartnerResource::make($user),
        ]);
    }

    public function orders(): AnonymousResourceCollection
    {
        $user = auth()->user();

        $orders = Order::query()
            ->where('partner_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return OrderResource::collection($orders);
    }

    public function orderStatus(Request $request): OrderResource
    {
        $request->validate([
            'order_id' => 'required|numeric|exists:orders,id',
            'status' => 'required|numeric|in:3,6',
        ]);

        $user = auth()->user();

        $order = Order::query()
            ->where('partner_id', $user->id)
            ->where('id', $request->order_id)
            ->first();

        $order->status = $request->status;
        $order->save();
        $order->refresh();

        return OrderResource::make($order);
    }
}
