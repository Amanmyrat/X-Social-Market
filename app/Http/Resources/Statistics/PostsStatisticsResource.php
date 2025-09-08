<?php

namespace App\Http\Resources\Statistics;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostsStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'active_posts_count' => $this->resource['active_posts_count'],
            'most_viewed_post' => $this->resource['most_viewed_post'],
            'most_favorited_post' => $this->resource['most_favorited_post'],
            'most_bookmarked_post' => $this->resource['most_bookmarked_post'],
        ];
    }
}
