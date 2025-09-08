<?php

namespace App\Http\Resources\Story;

use App\Http\Resources\UserSimpleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryTagResource extends JsonResource
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
            'name' => $this->resource->name,
            'dx' => $this->resource->dx,
            'dy' => $this->resource->dy,
            'user' => new UserSimpleResource($this->whenLoaded('user')),
            'text_options' => $this->resource->text_options,
        ];
    }
}
