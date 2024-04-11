<?php

namespace App\Http\Resources\Statistics;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'post' => $this->resource['post'],
            'view_count' => $this->resource['view_count'],
            'followers_distribution' => $this->resource['followers_distribution'],
            'favorite_count' => $this->resource['favorite_count'],
            'comment_count' => $this->resource['comment_count'],
            'bookmark_count' => $this->resource['bookmark_count'],
        ];
    }
}
