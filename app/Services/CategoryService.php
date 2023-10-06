<?php

namespace App\Services;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryService
{
    public static function create(CategoryRequest $request): void
    {
        Category::create($request->validated());
    }
}
