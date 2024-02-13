<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Services\PostCommentService;
use App\Transformers\CommentTransformer;
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
        PostCommentService::addComment($request, $post);

        return $this->respondWithMessage(trans('notification.comment_success'));
    }
}
