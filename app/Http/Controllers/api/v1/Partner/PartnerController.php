<?php

namespace App\Http\Controllers\api\v1\Partner;

use App\Http\Controllers\Controller;
use App\Http\Resources\Partner\PartnerResource;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function __construct(){}

    public function login(Request $request): PartnerResource
    {
        $request->validate([
            'phone' => 'required|numeric|exists:partners,phone',
            'password' => 'required|string',
        ]);

        $partner = auth('partner')->attempt($request->only('phone', 'password'));

        return new PartnerResource($partner);
    }
}
