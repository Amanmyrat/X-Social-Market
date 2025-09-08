<?php

namespace App\Services\Admin;

use App\Models\Post;
use App\Traits\SortableTrait;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    use SortableTrait;

    public function list(int $limit, array $conditions = [], ?string $search_query = null, ?string $sort = null): LengthAwarePaginator
    {
        $query = Post::with(['user', 'category', 'media'])->when(isset($search_query), function ($query) use ($search_query) {
            $search_query = '%'.$search_query.'%';

            return $query->whereHas('user', function ($q) use ($search_query) {
                $q->where('username', $search_query);
            })->orWhere('caption', 'LIKE', $search_query)
                ->orWhere('description', 'LIKE', $search_query);
        });

        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        $this->applySorting($query, $sort, ['caption', 'is_active', 'created_at', 'price']);

        return $query->paginate($limit);
    }
}
