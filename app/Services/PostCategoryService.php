<?php

namespace App\Services;

use App\Http\Requests\CategoryRequest;
use App\Models\PostCategory;

class PostCategoryService
{
    public static function create(CategoryRequest $request): void
    {
        PostCategory::create($request->validated());
    }
}
