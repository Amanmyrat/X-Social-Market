<?php

namespace App\Http\Resources\Admin\PostReport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'caption' => $this->resource->caption,
            'user' => $this->resource->user->username,
            'media_type' => $this->resource->media_type,
            'media' => $this->resource->first_image_urls,
            'lastReport' => [
                'id' => $this->resource->latestReport->id,
                'message' => $this->resource->latestReport->message,
                'report_type' => $this->resource->latestReport->reportType->title,
                'created_at' => $this->resource->latestReport->created_at,
            ],
            'reports_count' => $this->resource->reports_count,
        ];
    }
}
