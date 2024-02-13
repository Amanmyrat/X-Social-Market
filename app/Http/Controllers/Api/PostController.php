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
     */
    public function create(PostRequest $request): JsonResponse
    {
        PostService::create($request);

        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully created a new post',
            ]
        );
    }

    /**
     * Delete post
     */
    public function delete(Post $post): JsonResponse
    {
        $post->delete();

        return $this->respondWithMessage('Successfully deleted');
    }

    /**
     * My posts list
     */
    public function myPosts(): JsonResponse
    {
        return $this->respondWithCollection(auth('sanctum')->user()->posts, new PostTransformer());
    }

    /**
     * All posts list
     */
    public function allPosts(): JsonResponse
    {
        return $this->respondWithCollection(Post::withCount(['favorites', 'comments', 'views'])->get(), new PostTransformer());
    }

    /**
     * User posts list
     */
    public function userPosts($user): JsonResponse
    {
        $user = User::findOrFail($user);

        return $this->respondWithCollection($user->posts, new PostTransformer());
    }

    /**
     * Following users posts list
     */
    public function followingPosts(): JsonResponse
    {
        return $this->respondWithCollection(auth('sanctum')->user()->followings, new UserPostTransformer());
    }

    /**
     * Search posts
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['search_query' => ['required','string']]);
        return $this->respondWithPaginator(PostService::searchPosts($request), new PostTransformer());
    }

    /**
     * Post details
     */
    public function postDetails(Post $post): JsonResponse
    {
        return $this->respondWithItem($post->loadCount(['favorites', 'comments', 'views']), new PostTransformer());
    }
}
