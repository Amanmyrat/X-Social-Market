<?php

namespace App\Transformers;

use App\Models\Category;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform(Category $category): array
    {
        return [
            'id' => $category->id,
            'title' => $category->title,
            'description' => $category->description,
            'icon' => url('uploads/categories/'.$category->icon),
            'is_active' => $category->is_active,
            'has_product' => $category->has_product,

        ];
    }
}
