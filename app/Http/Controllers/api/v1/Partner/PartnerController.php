<?php

namespace App\Http\Controllers\api\v1\Partner;

use App\Enum\Partner\PartnerStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'token' => $token,
            'partner' => [
                'name' => $user->name,
                'country_code' => $user->country_code,
                'phone' => $user->phone,
            ],
            'status' => [
                'id' => $user->status,
                'name' => PartnerStatusEnum::tryFrom($user->status)->getLabel(),
            ]
        ]);
    }

}
