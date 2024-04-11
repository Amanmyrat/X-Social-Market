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
            'followers_count' => $this->resource['new_followers_count'],
            'unfollowers_count' => $this->resource['new_unfollowers_count'],
            'gender_distribution' => $this->resource['gender_distribution_new_followers'],
            'age_distribution' => $this->resource['age_distribution_new_followers'],
        ];
    }
}
