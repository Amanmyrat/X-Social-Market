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
        $current = $this->resource['current'];
        $previous = $this->resource['previous'];

        return [
            'profile_views_count' => $current['profileViewsCount'],
            'profile_views_count_previous' => $previous['profileViewsCount'] ?? null,
            'active_users_count' => $current['postEngagementsCount'],
            'active_users_count_previous' => $previous['postEngagementsCount'] ?? null,
            'new_followers_count' => $current['newFollowersCount'],
            'new_followers_count_previous' => $previous['newFollowersCount'] ?? null,
            'new_post_count' => $current['postCount'],
            'new_post_count_previous' => $previous['postCount'] ?? null,
            'best_post' => $this->formatBestPost($current['bestPost']),
            'best_post_previous' => $previous ? $this->formatBestPost($previous['bestPost']) : null,
        ];
    }

    private function formatBestPost($post): ?array
    {
        return $post != null ? [
            'caption' => $post->caption,
            'view_count' => $post->view_count,
            'active_users_count' => $post->engaged_users_count,
            'media_type' => $post->media_type,
            'media' => $post->first_image_urls,
        ] : null;
    }
}
