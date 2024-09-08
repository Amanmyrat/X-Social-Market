<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\UserSimpleResource;
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
            'type' => $this->resource->type,
            'caption' => $this->resource->caption,
            'price' => $this->resource->price,
            'media' => $this->resource->first_image_urls,
            'user' => new UserSimpleResource($this->whenLoaded('user')),
        ];
    }
}
