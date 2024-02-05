<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Transformers\CategoryTransformer;
use Illuminate\Http\JsonResponse;

class CategoryController extends ApiBaseController
{
    /**
     * Categories list
     * @return JsonResponse
     */
    public function categories(): JsonResponse
    {
        return $this->respondWithCollection(Category::where('is_active', true)->get(), new CategoryTransformer(false));
    }

}
