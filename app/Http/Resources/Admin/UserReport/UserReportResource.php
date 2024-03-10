<?php

namespace App\Http\Resources\Admin\UserReport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'username' => $this->resource->username,
            'full_name' => $this->resource->profile?->full_name,
            'profile_image' => $this->resource->profile?->profile_image ? url('uploads/user/profile/'.$this->resource->profile?->profile_image) : null,
            'lastReport' => [
                'id' => $this->resource->latestReportAgainst->id,
                'message' => $this->resource->latestReportAgainst->message,
                'report_type' => $this->resource->latestReportAgainst->reportType->title,
                'created_at' => $this->resource->latestReportAgainst->created_at,
            ],
            'reports_count' => $this->resource->reports_count,
        ];
    }
}
