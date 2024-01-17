<?php

namespace App\Services;

use App\Http\Requests\CategoryRequest;
use App\Models\PostCategory;

class PostCategoryService
{
    /**
     * @param CategoryRequest $request
     */
    public static function create(CategoryRequest $request): void
    {
        PostCategory::create($request->validated());
    }
}
