<?php

namespace App\Http\Resources\Partner;

use App\Enum\Partner\PartnerStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
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
            'name' => $this->name,
            'country_code' => $this->country_code,
            'phone' => $this->phone,
            'status' => [
                'status' => $this->status,
                'name' => PartnerStatusEnum::tryFrom($this->status->value)->getLabel(),
            ],
        ];
    }
}
