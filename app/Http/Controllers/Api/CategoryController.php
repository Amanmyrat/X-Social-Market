<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends ApiBaseController
{
    /**
     * Categories list
     */
    public function categories(): AnonymousResourceCollection
    {
        $categories = Category::where('is_active', true)->get();

        return CategoryResource::collection($categories);
    }
}
