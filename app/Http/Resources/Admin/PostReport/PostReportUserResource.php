<?php

namespace App\Http\Resources\Admin\PostReport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostReportUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->user->id,
            'username' => $this->resource->user->username,
            'full_name' => $this->resource->user->profile?->full_name,
            'image' => $this->resource->user->profile?->image_urls,
            'report_type' => $this->resource->reportType->title,
            'message' => $this->resource->message,
            'created_at' => $this->resource->created_at,
        ];
    }
}
