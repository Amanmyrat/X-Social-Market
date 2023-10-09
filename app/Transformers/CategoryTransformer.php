<?php

namespace App\Transformers;

use App\Models\PostCategory;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    #[ArrayShape(['id' => "mixed", 'title' => "mixed", 'description' => "mixed"])]
    public function transform(PostCategory $category): array
    {
        return [
            'id' => $category->id,
            'title' => $category->title,
            'description' => $category->description
        ];
    }
}
