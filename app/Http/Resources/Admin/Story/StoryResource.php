<?php

namespace App\Http\Resources\Admin\Story;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'image' => $this->resource->image_urls,
            'is_active' => $this->resource->is_active,
            'created_at' => $this->resource->created_at,
            'user' => [
                'id' => $this->resource->user->id,
                'username' => $this->resource->user->username,
                'image' => $this->resource->user->profile->image_urls ?? null,
            ],
        ];
    }
}
