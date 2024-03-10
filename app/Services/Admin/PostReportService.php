<?php

namespace App\Services\Admin;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PostReportService
{
    public function list(int $limit, ?string $search_query = null): LengthAwarePaginator
    {
        return Post::withCount(['postReports as reports_count'])
            ->with(['media', 'user', 'latestReport' => function ($query) {
                $query->with(['reportType']);
            }])
            ->has('postReports')
            ->when($search_query, function ($query) use ($search_query) {
                $search_query = '%'.$search_query.'%';
                $query->where('caption', 'like', $search_query);
            })
            ->orderByDesc('reports_count')
            ->paginate($limit);
    }

    public function getUsersWhoReportedPost(Post $post): Collection
    {
        return $post->postReports()->with(['user.profile', 'reportType'])->get();
    }
}
