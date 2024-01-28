<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CategoryCreateRequest;
use App\Models\Category;
use App\Services\CategoryService;
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
        return $this->respondWithCollection(Category::all(), new CategoryTransformer());
    }

}
