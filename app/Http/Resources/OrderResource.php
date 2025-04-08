<?php

namespace App\Http\Resources;

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
            'products' => CartProductResource::collection($this->cart->products),
            'total_price' => $this->total_price,
            'address' => $this->address,
            'phone' => $this->phone,
            'time' => $this->time,
            'status' => $this->status->name,
            'create_at' => $this->create_at
        ];
    }
}
