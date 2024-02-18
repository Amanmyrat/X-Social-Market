<?php

namespace App\Http\Resources\Admin;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
{
    private bool $detailsEnabled;

    public function __construct(Color $resource, bool $detailsEnabled = false)
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
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
            'is_active' => $this->is_active,
            'products_count' => 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ] : [
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
            'is_active' => $this->is_active,
            'products_count' => 0,
            'created_at' => $this->created_at,
        ];
    }
}
