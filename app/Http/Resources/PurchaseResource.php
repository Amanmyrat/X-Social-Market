<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
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
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'price_tnt' => (float) $this->price_tnt,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'product' => new MarketProductResource($this->whenLoaded('product')),
            'user' => $this->when($request->user()->id === $this->user_id || $request->user()->is_admin ?? false, function () {
                return [
                    'id' => $this->user->id,
                    'username' => $this->user->username,
                    'phone' => $this->user->phone,
                ];
            }),
        ];
    }
}

