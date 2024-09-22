<?php

namespace App\Http\Resources\Story;

use App\Http\Resources\Post\PostSimpleResource;
use App\Http\Resources\UserSimpleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    private static array $data;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isViewed = in_array($this->resource->id, self::$data['viewedStoryIds'] ?? []);
        $isFavorite = in_array($this->resource->id, self::$data['favoriteStoryIds'] ?? []);

        return [
            'id' => $this->resource->id,
            'image' => $this->resource->image_urls,
            'isViewed' => $isViewed,
            'isFavorite' => $isFavorite,
            'valid_until' => $this->resource->valid_until,
            'created_at' => $this->resource->created_at,
            'is_active' => $this->resource->is_active,
            'post' => new PostSimpleResource($this->whenLoaded('post')),
            'tags' => StoryTagResource::collection($this->whenLoaded('tags')),
        ];
    }

    public static function customCollection($resource, $data): AnonymousResourceCollection
    {
        self::$data = $data;

        return parent::collection($resource);
    }
}
