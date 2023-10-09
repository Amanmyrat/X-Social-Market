<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostBookmark;
use Illuminate\Support\Collection;

class PostBookmarkService
{
    public static function add(Post $post): string
    {
        $message = trans('notification.bookmark_success');
        if ($post->getIsBookmark()) {
            $post->myBookmarks()->delete();
            $message = trans('notification.bookmark_remove_success');
        } else {
            $bookmark = new PostBookmark();
            $bookmark->user()->associate(auth()->user());
            $bookmark->post()->associate($post);
            $bookmark->save();
        }
        return $message;
    }

    public static function get(): Collection
    {
        $bookmarks = auth()->user()->bookmarks->pluck('post_id')->toArray();

        return Post::whereIn('id', $bookmarks)->get();
    }

}
