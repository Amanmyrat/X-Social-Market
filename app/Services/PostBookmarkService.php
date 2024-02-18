<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostBookmark;
use App\Models\User;
use App\Traits\PreparesPostQuery;
use Auth;
use Illuminate\Support\Collection;

class PostBookmarkService
{
    use PreparesPostQuery;

    public function add(Post $post): string
    {
        /** @var User $user */
        $user = Auth::user();
        $isBookmark = $post->bookmarks()->where('user_id', $user->id)->exists();

        if ($isBookmark) {
            $post->bookmarks()->where('user_id', $user->id)->delete();
            $message = 'Bookmark remove success';
        } else {
            $bookmark = new PostBookmark();
            $bookmark->user()->associate($user);
            $bookmark->post()->associate($post);
            $bookmark->save();
            $message = 'Bookmark success';
        }

        return $message;
    }

    public function getUserBookmarkPosts(User $user): Collection
    {
        $bookmarkPostIds = $user->bookmarks()->pluck('post_id')->toArray();

        $postsQuery = $this->getPostsByIdsQuery($bookmarkPostIds);

        return $postsQuery->get();
    }
}
