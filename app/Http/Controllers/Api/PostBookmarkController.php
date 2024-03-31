<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Services\PostBookmarkService;
use App\Traits\HandlesUserPostInteractions;
use App\Traits\PreparesPostQuery;
use App\Transformers\PostTransformer;
use Auth;
use Illuminate\Http\JsonResponse;

class PostBookmarkController extends ApiBaseController
{
    use HandlesUserPostInteractions, PreparesPostQuery;

    public function __construct(protected PostBookmarkService $service)
    {
        parent::__construct();
    }

    /**
     * Users bookmark posts
     */
    public function bookmarks(): JsonResponse
    {
        $user = Auth::user();
        $posts = $this->service->getUserBookmarkPosts($user);
        $userInteractionsDTO = $this->getUserInteractionsDTO();

        return $this->respondWithCollection($posts, new PostTransformer($userInteractionsDTO));
    }

    /**
     * Change bookmark
     */
    public function change(Post $post): JsonResponse
    {
        $message = $this->service->add($post);

        return $this->respondWithMessage($message);
    }
}
