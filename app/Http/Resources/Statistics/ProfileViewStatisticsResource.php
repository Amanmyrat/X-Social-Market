<?php

namespace App\Http\Resources\Statistics;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileViewStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_views' => $this->resource['total_views'],
            'total_views_previous' => $this->resource['total_views_previous'],
            'total_views_change' => $this->resource['total_views_change'],
            'followers_distribution' => $this->resource['followers_distribution'],
            'gender_distribution' => $this->resource['gender_distribution'],
            'age_distribution' => $this->resource['age_distribution'],
        ];
    }
}
