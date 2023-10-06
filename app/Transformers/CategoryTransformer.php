<?php

namespace App\Transformers;

use App\Models\Category;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    #[ArrayShape(['id' => "mixed", 'title' => "mixed", 'description' => "mixed"])]
    public function transform(Category $category): array
    {
        return [
            'id' => $category->id,
            'title' => $category->title,
            'description' => $category->description
        ];
    }
}
