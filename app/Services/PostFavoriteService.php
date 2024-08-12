<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostFavorite;
use App\Models\User;
use App\Traits\PreparesPostQuery;
use Illuminate\Support\Collection;

class PostFavoriteService
{
    use PreparesPostQuery;

    public function change(Post $post, User $user): string
    {
        $favorite = $post->favorites()->where('user_id', $user->id)->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Favorite remove';
        } else {
            $favorite = new PostFavorite();
            $favorite->user()->associate($user);
            $favorite->post()->associate($post);
            $favorite->save();
            $message = 'Favorite success';

            NotificationService::createPostInteractionNotificationToPostAuthor($favorite, $favorite->post_id);
        }

        return $message;
    }

    public function getUserFavoritePosts(User $user): Collection
    {
        $favoritePostIds = $user->favorites()->pluck('post_id')->toArray();
        $postsQuery = $this->getPostsByIdsQuery($favoritePostIds);

        return $postsQuery->get();
    }
}
