<?php

namespace App\Http\Controllers\api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @throws Exception
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|numeric',
            'password' => 'required|string',
        ]);

        $user = Customer::query()->where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 422);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'token' => $token,
            'customer' => [
                'name' => $user->name,
                'country_code' => $user->country_code,
                'phone' => $user->phone,
            ]
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|unique:customers,phone',
            'location' => 'required|string|max:500',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = $customer->createToken('authToken')->plainTextToken;

        return response()->json([
            'token' => $token,
            'customer' => [
                'name' => $customer->name,
                'country_code' => $customer->country_code,
                'phone' => $customer->phone
            ],
        ], 201);
    }
}
