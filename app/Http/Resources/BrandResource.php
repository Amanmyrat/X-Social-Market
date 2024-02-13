<?php

namespace App\Http\Resources;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\Pure;

class BrandResource extends JsonResource
{
    private bool $detailsEnabled;

    #[Pure]
    public function __construct(Brand $resource, bool $detailsEnabled = false)
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
            'type' => $this->type,
            'is_active' => $this->is_active,
            'products_count' => 0,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ] : [
            'id' => $this->id,
            'title' => $this->title,
            'is_active' => $this->is_active,
            'products_count' => 0,
        ];
    }
}
