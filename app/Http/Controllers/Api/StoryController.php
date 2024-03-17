<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoryRequest;
use App\Models\Story;
use App\Models\User;
use App\Services\StoryService;
use App\Transformers\StoryTransformer;
use App\Transformers\UserStoryTransformer;
use Auth;
use Illuminate\Http\JsonResponse;
use Throwable;

class StoryController extends ApiBaseController
{
    public function __construct(protected StoryService $service)
    {
        parent::__construct();
    }

    /**
     * Create story
     * @throws Throwable
     */
    public function create(StoryRequest $request): JsonResponse
    {
        $this->service->create($request->validated(), Auth::user());

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully created a new story',
        ]);
    }

    /**
     * My stories list
     */
    public function myStories(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->respondWithCollection($user->stories, new StoryTransformer());
    }

    /**
     * User stories list
     */
    public function userStories(User $user): JsonResponse
    {
        return $this->respondWithCollection($user->stories, new StoryTransformer());
    }

    /**
     * Following users stories list
     */
    public function followingStories(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->respondWithCollection($user->followings, new UserStoryTransformer());
    }
}
