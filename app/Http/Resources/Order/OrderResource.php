<?php

namespace App\Http\Resources\Order;

use App\Enum\Product\ProductStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'price' => $this->price,
            'note' => $this->note,
            'status' => [
                'status' => $this->status,
                'name' => ProductStatusEnum::from($this->status)->getLabel(),
            ],
            'products' => $this->orderProduct->map(function ($product) {
                return [
                    'product_id' => $product->product_id,
                    'name' => $product->product->name,
                    'count' => $product->count,
                ];
            })
        ];
    }
}
