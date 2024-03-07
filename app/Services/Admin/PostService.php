<?php

namespace App\Services\Admin;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    public function list(int $limit, ?string $search_query = null): LengthAwarePaginator
    {
        return Post::with(['user', 'category', 'media'])->when(isset($search_query), function ($query) use ($search_query) {
            $search_query = '%'.$search_query.'%';

            return $query->whereHas('user', function ($q) use ($search_query) {
                $q->where('username', $search_query);
            })->where('caption', 'LIKE', $search_query)
                ->orWhere('description', 'LIKE', $search_query);
        })->latest()->paginate($limit);
    }
}
