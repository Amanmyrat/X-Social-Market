<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryCreateRequest;
use App\Http\Requests\Category\CategoryDeleteRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Size;
use App\Services\UniversalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminCategoryController extends Controller
{
    public function __construct(protected UniversalService $service)
    {
        $this->service->setModel(new Size());
    }

    /**
     * Categories list
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function list(Request $request): AnonymousResourceCollection
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;

        $categories = $this->service->list($limit, $query);

        return CategoryResource::collection($categories);
    }

    /**
     * Category details
     * @param Category $category
     * @return CategoryResource
     */
    public function categoryDetails(Category $category): CategoryResource
    {
        return new CategoryResource($category->loadCount('posts'), true);
    }

    /**
     * Create category
     * @param CategoryCreateRequest $request
     * @return JsonResponse
     */
    public function create(CategoryCreateRequest $request): JsonResponse
    {
        $this->service->create($request->validated());
        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully created a new category'
        ]);
    }

    /**
     * Update category
     * @param Category $category
     * @param CategoryUpdateRequest $request
     * @return CategoryResource
     */
    public function update(Category $category, CategoryUpdateRequest $request): CategoryResource
    {
        /** @var Category $category */
        $category = $this->service->update($category, $request->validated());
        return new CategoryResource($category->loadCount('posts'), true);
    }

    /**
     * Delete categories
     * @param CategoryDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(CategoryDeleteRequest $request): JsonResponse
    {
        $this->service->delete($request->categories);

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully deleted'
        ]);
    }
}
