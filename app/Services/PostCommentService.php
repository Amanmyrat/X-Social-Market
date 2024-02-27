<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostComment;
use Auth;

class PostCommentService
{
    public static function addComment($validated, Post $post): void
    {
        $comment = new PostComment([
            'comment' => $validated['comment'],
            'parent_id' => $validated['parent_id'] ?? 0,
        ]);

        $comment->user()->associate(Auth::user());
        $comment->post()->associate($post);
        $comment->save();

        NotificationService::createPostNotification($comment, $comment->post_id);

    }
}
