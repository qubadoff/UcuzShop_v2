<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => [
                'id' => $this->category->id ?? null,
                'name' => $this->category->name ?? null,
            ],
            'name' => $this->name,
            'price' => $this->price,
            'stock_count' => $this->stock_count,
            'images' => collect($this->images)->map(function ($image) {
                return url('/') . '/storage/' . $image;
            }),
        ];
    }
}
