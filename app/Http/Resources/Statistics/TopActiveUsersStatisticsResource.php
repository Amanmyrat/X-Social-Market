<?php

namespace App\Http\Resources\Statistics;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopActiveUsersStatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->resource['user']->id,
                'username' => $this->resource['user']->username,
                'full_name' => $this->resource['user']->profile?->full_name,
                'image' => $this->resource['user']->profile?->image_urls,
            ],
            'details' => $this->resource['details'],
            'total_engagements' => $this->resource['total_engagements']
        ];
    }
}
