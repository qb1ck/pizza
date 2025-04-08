<?php

namespace App\Http\Resources;

use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminOrderResource extends JsonResource
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

            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],

            'products' => CartProductResource::collection($this->cart->products),

            'total_price' => $this->total_price,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,

            'status_id' => $this->status_id,
            'status_label' => OrderStatusEnum::from($this->status_id)->label(),

            'time' => $this->time,
            'created_at' => $this->created_at,
        ];
    }
}
