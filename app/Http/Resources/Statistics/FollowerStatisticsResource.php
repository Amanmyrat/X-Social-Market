<?php

namespace App\Http\Resources\Statistics;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowerStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_followers_count' => $this->resource['total_followers_count'],
            'total_followers_previous' => $this->resource['total_followers_previous'],
            'total_followers_change' => $this->resource['total_followers_change'],
            'new_followers_count' => $this->resource['new_followers_count'],
            'new_unfollowers_count' => $this->resource['new_unfollowers_count'],
            'gender_distribution' => $this->resource['gender_distribution_new_followers'],
            'age_distribution' => $this->resource['age_distribution_new_followers'],
        ];
    }
}
