<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostFavorite;
use Illuminate\Support\Collection;

class PostFavoriteService
{
    public static function add(Post $post): string
    {
        $message = trans('notification.favorite_success');
        if ($post->getIsFavorite()) {
            $post->myFavorites()->delete();
            $message = trans('notification.favorite_remove_success');
        } else {
            $favorite = new PostFavorite();
            $favorite->user()->associate(auth('sanctum')->user());
            $favorite->post()->associate($post);
            $favorite->save();
        }

        return $message;
    }

    public static function get(): Collection
    {
        $favorites = auth('sanctum')->user()->favorites->pluck('post_id')->toArray();

        return Post::whereIn('id', $favorites)->get();
    }
}
