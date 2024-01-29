<?php

namespace App\Transformers;

use App\Models\Brand;
use League\Fractal\TransformerAbstract;

class BrandTransformer extends TransformerAbstract
{
    private bool $detailsEnabled;

    public function __construct(bool $detailsEnabled)
    {
        $this->detailsEnabled = $detailsEnabled;
    }

    public function transform(Brand $brand): array
    {
        return $this->detailsEnabled ? [
            'id' => $brand->id,
            'title' => $brand->title,
            'type' => $brand->type,
            'is_active' => $brand->is_active,
            'products_count' => 0,
            'created_at' => $brand->created_at,
            'updated_at' => $brand->updated_at,

        ] : [
            'id' => $brand->id,
            'title' => $brand->title,
            'is_active' => $brand->is_active,
            'products_count' => 0,
        ];
    }
}
