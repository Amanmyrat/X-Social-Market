<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use App\Transformers\PostTransformer;
use App\Transformers\UserPostTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends ApiBaseController
{
    /**
     * Create post
     * @param PostRequest $request
     * @return JsonResponse
     */
    public function create(PostRequest $request): JsonResponse
    {
        PostService::create($request);

        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully created a new post'
            ]
        );
    }

    /**
     * My posts list
     * @return JsonResponse
     */
    public function myPosts(): JsonResponse
    {
        return $this->respondWithCollection(auth()->user()->posts, new PostTransformer());
    }

    /**
     * All posts list
     * @return JsonResponse
     */
    public function allPosts(): JsonResponse
    {
        return $this->respondWithCollection(Post::all(), new PostTransformer());
    }

    /**
     * User posts list
     * @param $user
     * @return JsonResponse
     */
    public function userPosts($user): JsonResponse
    {
        $user = User::findOrFail($user);
        return $this->respondWithCollection($user->posts, new PostTransformer());
    }

    /**
     * Following users posts list
     * @return JsonResponse
     */
    public function followingPosts(): JsonResponse
    {
        return $this->respondWithCollection(auth()->user()->followings, new UserPostTransformer());
    }

    /**
     * Search posts
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        return $this->respondWithPaginator(PostService::searchPosts($request), new PostTransformer());
    }

    /**
     * Post details
     * @param Post $post
     * @return JsonResponse
     */
    public function postDetails(Post $post): JsonResponse
    {
        return $this->respondWithItem($post, new PostTransformer());
    }
}
