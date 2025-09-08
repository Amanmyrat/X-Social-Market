<?php

namespace App\Http\Resources\Admin\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'name' => $this->resource->name,
            'surname' => $this->resource->surname,
            'phone' => $this->resource->phone,
            'email' => $this->resource->email,
            'role' => $this->resource->roles()->first()['display_name'] ?? null,
            'permissions' => $this->resource->getAllPermissions()->pluck('name'),
            'last_activity' => $this->resource->last_activity,
            'is_active' => $this->resource->is_active,
            'image' => $this->resource->image_urls,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,

        ] : [
            'id' => $this->resource->id,
            'name' => $this->resource->name.' '.$this->resource->surname,
            'phone' => $this->resource->phone,
            'email' => $this->resource->email,
            'role' => $this->resource->roles()->first()['display_name'] ?? null,
            'last_activity' => $this->resource->last_activity,
            'is_active' => $this->resource->is_active,
            'image' => $this->resource->image_urls,
        ];
    }
}
