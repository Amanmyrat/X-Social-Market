<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;

class PostCommentService
{
    public static function addComment(Request $request, Post $post): void
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:255'],
            'parent_id' => ['filled', 'int', 'exists:post_comments,id'],
        ]);

        $comment = new PostComment([
            'comment' => $validated['comment'],
            'parent_id' => $validated['parent_id'] ?? 0,
        ]);

        $comment->user()->associate(auth('sanctum')->user());
        $comment->post()->associate($post);
        $comment->save();
    }
}
