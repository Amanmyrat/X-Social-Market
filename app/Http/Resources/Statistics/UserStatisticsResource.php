<?php

namespace App\Http\Resources\Statistics;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'profile_views_count' => $this->resource['profileViewsCount'],
            'active_users_count' => $this->resource['postEngagementsCount'],
            'new_followers_count' => $this->resource['newFollowersCount'],
            'new_post_count' => $this->resource['postCount'],
            'best_post' => $this->resource['bestPost'] != null ? [
                'caption' => $this->resource['bestPost']->caption,
                'view_count' => $this->resource['bestPost']->view_count,
                'active_users_count' => $this->resource['bestPost']->engaged_users_count,
                'media_type' => $this->resource['bestPost']->media_type,
                'media' => $this->resource['bestPost']->first_image_urls,
            ] : null,
        ];
    }
}
