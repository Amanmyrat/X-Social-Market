<?php

namespace App\Http\Resources\Admin\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'surname' => $this->resource->surname,
            'email' => $this->resource->email,
            'profile_image' => $this->resource->profile_image ? url('uploads/admin/'.$this->resource->profile_image) : null,
            'role' => $this->resource->getRoleNames()->first(),
            'permissions' => $this->resource->getAllPermissions()->pluck('name'),
        ];
    }
}
