<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PostComment\PostCommentCreateRequest;
use App\Models\Post;
use App\Models\PostComment;
use App\Services\PostCommentService;
use App\Transformers\CommentTransformer;
use Auth;
use Illuminate\Http\JsonResponse;

class PostCommentController extends ApiBaseController
{
    public function __construct(protected PostCommentService $service)
    {
        parent::__construct();
    }

    /**
     * Post comments
     */
    public function comments(Post $post): JsonResponse
    {
        $comments = $post->activeComments()->whereNull('blocked_at')->get();

        return $this->respondWithCollection($comments, new CommentTransformer());
    }

    /**
     * Add comment to given product
     */
    public function addComment(Post $post, PostCommentCreateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $this->service->addComment($validated, $post, Auth::user());

        return $this->respondWithMessage('Comment added');
    }

    /**
     * Delete comment
     */
    public function deleteComment(PostComment $postComment): JsonResponse
    {
        abort_if($postComment->user_id != Auth::id(), 403, 'Only author can delete the comment');

        $postComment->delete();

        return $this->respondWithMessage('Comment deleted');
    }
}
