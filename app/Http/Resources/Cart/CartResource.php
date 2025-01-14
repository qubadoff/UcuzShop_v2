<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
            ],
            'product' => [
                'id' => $this->product->id ?? null,
                'name' => $this->product->name ?? null,
                'price' => $this->product->price ?? null
            ],
            'count' => $this->count
        ];
    }
}
