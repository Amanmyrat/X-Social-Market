<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Services\PostCommentService;
use App\Transformers\CommentTransformer;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostCommentController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function comments(Post $post): JsonResponse
    {
        return $this->respondWithCollection($post->comments, new CommentTransformer());
    }

    /**
     * Add comment to given product
     */
    public function addComment(Post $post, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:255'],
            'parent_id' => [
                'sometimes',
                'int',
                function ($attribute, $value, $fail) use ($post) {
                    if ($value !== 0 && ! is_null($value)) {
                        $parentComment = DB::table('post_comments')->where('id', $value)->first(['post_id']);
                        if (! $parentComment) {
                            $fail('The selected '.$attribute.' is invalid.');
                        } elseif ($parentComment->post_id != $post->id) {
                            $fail('The '.$attribute.' does not belong to the provided post.');
                        }
                    }
                },
            ],
        ]);
        PostCommentService::addComment($validated, $post);

        return $this->respondWithMessage(trans('notification.comment_success'));
    }
}
