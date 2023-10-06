<?php

namespace App\Http\Controllers\Api;

use App\Models\Story;
use App\Models\User;
use App\Services\StoryService;
use App\Transformers\StoryTransformer;
use App\Transformers\UserStoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoryController extends ApiBaseController
{
    /**
     * Create story
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        StoryService::create($request);

        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully created a new story'
            ]
        );
    }

    /**
     * My stories list
     * @return JsonResponse
     */
    public function myStories(): JsonResponse
    {
        return $this->respondWithCollection(auth()->user()->stories, new StoryTransformer());
    }

    /**
     * User stories list
     * @param $user
     * @return JsonResponse
     */
    public function userStories($user): JsonResponse
    {
        $user = User::findOrFail($user);
        return $this->respondWithCollection($user->stories, new StoryTransformer());
    }

    /**
     * Following users stories list
     * @return JsonResponse
     */
    public function followingStories(): JsonResponse
    {
        return $this->respondWithCollection(auth()->user()->followings, new UserStoryTransformer());
    }
}
