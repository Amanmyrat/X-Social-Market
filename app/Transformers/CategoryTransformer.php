<?php

namespace App\Transformers;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    private bool $detailsEnabled;

    public function __construct(bool $detailsEnabled)
    {
        $this->detailsEnabled = $detailsEnabled;
    }

    public function transform(Category $category): array
    {
        return $this->detailsEnabled ? [
            'id' => $category->id,
            'title' => $category->title,
            'description' => $category->description,
            'icon' => url('uploads/categories/'.$category->icon),
            'is_active' => $category->is_active,
            'has_product' => $category->has_product,
            'posts_count' => $category->posts_count,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,

        ] : [
            'id' => $category->id,
            'title' => $category->title,
            'icon' => url('uploads/categories/'.$category->icon),
            'is_active' => $category->is_active,
            'posts_count' => $category->posts_count,
        ];
    }
}
