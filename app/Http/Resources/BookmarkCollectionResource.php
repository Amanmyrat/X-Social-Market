<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookmarkCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'bookmarks_count' => $this->bookmarks->count(),
            'bookmarks' => $this->when($this->relationLoaded('bookmarks'), function () {
                return $this->bookmarks->map(function ($bookmark) {
                    return [
                        'id'      => $bookmark->post->id ?? null,
                        'caption' => $bookmark->post->caption ?? null,
                        'media'   => $bookmark->post->image_urls ?? [],
                    ];
                });
            }, []),
        ];
    }
}
