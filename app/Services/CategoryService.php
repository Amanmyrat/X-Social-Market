<?php

namespace App\Services;

use App\Http\Requests\CategoryCreateRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class CategoryService
{
    /**
     * Create category
     *
     * @param array $data
     */
    public function create(array $data): void
    {
        $categoryImageName = $data['title'].'-'.time().'.'.$data['icon']->getClientOriginalExtension();
        $data['icon']->move(public_path('uploads/categories'), $categoryImageName);
        $data['icon'] = $categoryImageName;
        Category::create($data);
    }

    /**
     * Get category list
     * @param int $limit
     * @param string|null $search_query
     * @return LengthAwarePaginator
     */
    public function list(int $limit, string $search_query = null): LengthAwarePaginator
    {
        return Category::when(isset($search_query), function ($query) use ($search_query) {
                $search_query = '%' . $search_query . '%';
                return $query->where('title', 'LIKE', $search_query)
                    ->orWhere('description', 'LIKE', $search_query);
            })->latest()->paginate($limit);
    }

    /**
     * Update category
     *
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function update(Category $category, array $data): Category
    {;
        if(isset($data['icon'])){
            $categoryImageName = $data['title'] ?? $category->title.'-'.time().'.'.$data['icon']->getClientOriginalExtension();
            $data['icon']->move(public_path('uploads/categories'), $categoryImageName);
            $data['icon'] = $categoryImageName;
        }

        $category->update($data);

        return $category;
    }
}
