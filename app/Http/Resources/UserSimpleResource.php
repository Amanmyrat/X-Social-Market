<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSimpleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'username' => $this->resource->username,
            'full_name' => $this->resource->profile->full_name ?? null,
            'image' => $this->resource->profile->image_urls ?? null,
        ];
    }
}
