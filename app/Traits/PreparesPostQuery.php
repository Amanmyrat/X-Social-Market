<?php

namespace App\Traits;

use App\Models\Post;
use Auth;

trait PreparesPostQuery
{
    private function getPostsQuery()
    {
        return Post::with(['user.profile', 'media'])
            ->withAvg('ratings', 'rating')
            ->withCount(['favorites', 'comments'])
            ->activeAndNotBlocked(Auth::id());
    }

    private function getUserPostsQuery($user)
    {
        return Post::with(['media'])
            ->where('type','post')
            ->where('posts.user_id', $user->id)
            ->activeAndNotBlocked(Auth::id())
            ->latest();
    }

    private function getUserProductsQuery($user)
    {
        return Post::with(['media'])
            ->where('type','product')
            ->where('posts.user_id', $user->id)
            ->activeAndNotBlocked(Auth::id())
            ->latest();
    }

    private function getPostsByIdsQuery(array $postIds)
    {
        return $this->getPostsQuery()->latest()->whereIn('posts.id', $postIds);
    }
}
