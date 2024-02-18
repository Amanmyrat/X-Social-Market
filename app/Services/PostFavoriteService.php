<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostFavorite;
use App\Models\User;
use App\Traits\PreparesPostQuery;
use Auth;
use Illuminate\Support\Collection;

class PostFavoriteService
{
    use PreparesPostQuery;

    public function add(Post $post): string
    {
        /** @var User $user */
        $user = Auth::user();

        $isFavorite = $post->favorites()->where('user_id', $user->id)->exists();

        if ($isFavorite) {
            $post->favorites()->where('user_id', $user->id)->delete();
            $message = 'Favorite remove success';
        } else {
            $favorite = new PostFavorite();
            $favorite->user()->associate($user);
            $favorite->post()->associate($post);
            $favorite->save();
            $message = 'Favorite success';
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
