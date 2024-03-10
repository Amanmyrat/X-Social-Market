<?php

namespace App\Services\Admin;

use App\Models\Post;
use App\Traits\SortableTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PostReportService
{
    use SortableTrait;

    public function list(int $limit, ?string $search_query = null, ?string $sort = null): LengthAwarePaginator
    {
        $query = Post::withCount(['postReports as reports_count'])
            ->with(['media', 'user', 'latestReport' => function ($query) {
                $query->with(['reportType']);
            }])
            ->has('postReports')
            ->when($search_query, function ($query) use ($search_query) {
                $search_query = '%'.$search_query.'%';
                $query->where('caption', 'like', $search_query);
            });

        if (($sort == 'default')) {
            $query->orderByDesc('reports_count');
        } else {
            $this->applySorting($query, $sort, ['caption', 'is_active', 'created_at', 'reports_count']);
        }

        return $query->paginate($limit);
    }

    public function getUsersWhoReportedPost(Post $post): Collection
    {
        return $post->postReports()->with(['user.profile', 'reportType'])->get();
    }
}
