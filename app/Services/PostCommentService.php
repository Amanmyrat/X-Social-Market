<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;

class PostCommentService
{
    public function addComment($validated, Post $post, User $user): void
    {
        $comment = new PostComment([
            'comment' => $validated['comment'],
            'parent_id' => $validated['parent_id'] ?? 0,
        ]);

        $comment->user()->associate($user);
        $comment->post()->associate($post);
        $comment->save();

        NotificationService::createPostNotification($comment, $comment->post_id);

    }
}
