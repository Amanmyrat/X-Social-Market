<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoryRequest;
use App\Models\Story;
use App\Models\User;
use App\Services\StoryService;
use App\Transformers\StoryTransformer;
use App\Transformers\UserStoryTransformer;
use Illuminate\Http\JsonResponse;

class StoryController extends ApiBaseController
{
    /**
     * Create story
     */
    public function create(StoryRequest $request): JsonResponse
    {
        StoryService::create($request);

        return $this->respondWithArray([
            'success' => true,
            'message' => 'Successfully created a new story',
        ]
        );
    }

    /**
     * My stories list
     */
    public function myStories(): JsonResponse
    {
        return $this->respondWithCollection(auth('sanctum')->user()->stories, new StoryTransformer());
    }

    /**
     * User stories list
     */
    public function userStories($user): JsonResponse
    {
        $user = User::findOrFail($user);

        return $this->respondWithCollection($user->stories, new StoryTransformer());
    }

    /**
     * Following users stories list
     */
    public function followingStories(): JsonResponse
    {
        return $this->respondWithCollection(auth('sanctum')->user()->followings, new UserStoryTransformer());
    }
}
