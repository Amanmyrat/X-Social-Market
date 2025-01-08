<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostBookmark;
use App\Models\User;
use App\Traits\PreparesPostQuery;

class PostBookmarkService
{
    use PreparesPostQuery;

    public function add(Post $post, User $user, $collectionId = null): string
    {
        $query = $post->bookmarks()->where('user_id', $user->id);

        if ($collectionId) {
            $query->where('collection_id', $collectionId);
        }

        $bookmark = $query->first();

        if ($bookmark) {
            $bookmark->delete();
            $message = 'Bookmark removed';
        } else {
            $bookmark = new PostBookmark();
            $bookmark->user()->associate($user);
            $bookmark->post()->associate($post);

            if ($collectionId) {
                $bookmark->collection_id = $collectionId;
            }

            $bookmark->save();
            $message = 'Bookmark added';
        }

        return $message;
    }

}
