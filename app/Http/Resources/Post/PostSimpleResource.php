<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostSimpleResource extends JsonResource
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
            'caption' => $this->resource->caption,
            'price' => $this->resource->price,
            'media_type' => $this->resource->media_type,
            'media' => $this->resource->first_image_urls,
        ];
    }
}
