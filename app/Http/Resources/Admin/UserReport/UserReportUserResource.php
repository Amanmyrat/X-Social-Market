<?php

namespace App\Http\Resources\Admin\UserReport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserReportUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->reporter->id,
            'username' => $this->resource->reporter->username,
            'full_name' => $this->resource->reporter->profile?->full_name,
            'profile_image' => $this->resource->reporter->profile?->profile_image ? url('uploads/user/profile/'.$this->resource->reporter->profile?->profile_image) : null,
            'report_type' => $this->resource->reportType->title,
            'message' => $this->resource->message,
            'created_at' => $this->resource->created_at,
        ];
    }
}
