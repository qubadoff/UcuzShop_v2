<?php

namespace App\Http\Resources\Order;

use App\Enum\Order\OrderStatusEnum;
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
        $discountedData = 0;

        if ($this->discount > 0) {
            $discountedData = $this->price - ($this->price * $this->discount / 100);
        }

        return [
            'id' => $this->id,
            'price' => $this->price,
            'price_d' => $discountedData,
            'discount' => $this->discount,
            'note' => $this->note,
            'status' => [
                'status' => $this->status,
                'name' => OrderStatusEnum::tryFrom($this->status->value)->getLabel(),
            ],
            'customer' => [
                'id' => $this->customer_id,
                'name' => $this->customer->name,
                'phone' => $this->customer->phone,
                'address' => $this->customer->location,
            ],
            'products' => $this->orderProduct->map(function ($product) {
                return [
                    'product_id' => $product->product_id,
                    'name' => $product->product->name,
                    'price' => $product->product->price,
                    'count' => $product->count,
                    'images' => collect(optional($product->product)->images)->map(function ($image) {
                        return url('/') . '/storage/' . $image;
                    })->filter(),
                ];
            })
        ];
    }
}
