<?php

namespace App\Traits;

use App\Models\Post;

trait PreparesPostQuery
{
    private function getPostsQuery()
    {
        return Post::with(['user.profile', 'media'])
            ->withAvg('ratings', 'rating')
            ->withIsFollowing();
    }
    private function getUserPostsQuery($user)
    {
        return $user->posts()
            ->with(['user.profile', 'media'])
            ->withAvg('ratings', 'rating')
            ->withIsFollowing()
            ->latest();
    }

    private function getPostsByIdsQuery(array $postIds)
    {
        return $this->getPostsQuery()->latest()->whereIn('posts.id', $postIds);
    }

}
