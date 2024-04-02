<?php

namespace App\Http\Resources\Admin\ReportType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportTypeResource extends JsonResource
{
    private bool $detailsEnabled;

    public function __construct($resource, bool $detailsEnabled = false)
    {
        parent::__construct($resource);
        $this->detailsEnabled = $detailsEnabled;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return $this->detailsEnabled ? [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'is_active' => $this->resource->is_active,
            'message_required' => $this->resource->message_required,
            'post_reports_count' => $this->resource->post_reports_count,
            'story_reports_count' => $this->resource->story_reports_count,
            'user_reports_count' => $this->resource->user_reports_count,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,

        ] : [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'is_active' => $this->resource->is_active,
            'message_required' => $this->resource->message_required,
            'post_reports_count' => $this->resource->post_reports_count,
            'story_reports_count' => $this->resource->story_reports_count,
            'user_reports_count' => $this->resource->user_reports_count,
            'created_at' => $this->resource->created_at,
        ];
    }
}
