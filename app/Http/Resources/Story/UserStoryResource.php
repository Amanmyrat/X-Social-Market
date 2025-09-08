<?php

namespace App\Http\Resources\Story;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserStoryResource extends JsonResource
{
    private static array $data;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isViewed = true;
        foreach ($this->resource->stories as $story) {
            if (! in_array($story->id, self::$data['viewedStoryIds'])) {
                $isViewed = false;
                break;
            }
        }

        return [
            'id' => $this->resource->id,
            'username' => $this->resource->username,
            'full_name' => $this->resource->profile->full_name ?? null,
            'image' => $this->resource->profile->image_urls ?? null,
            'last_activity' => $this->resource->last_activity,
            'stories' => StoryResource::customCollection($this->resource->stories, self::$data),
            'isViewed' => $isViewed,
        ];
    }

    public static function customCollection($resource, $data): AnonymousResourceCollection
    {
        self::$data = $data;

        return parent::collection($resource);
    }
}
