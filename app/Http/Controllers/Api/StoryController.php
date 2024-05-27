<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoryRequest;
use App\Http\Resources\Story\StoryResource;
use App\Http\Resources\Story\UserStoryResource;
use App\Models\Story;
use App\Models\User;
use App\Services\StoryService;
use App\Traits\HandlesUserStoryInteractions;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class StoryController extends ApiBaseController
{
    use HandlesUserStoryInteractions;

    public function __construct(protected StoryService $service)
    {
        parent::__construct();
    }

    /**
     * Create story
     *
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
     * Delete story
     *
     * @throws Throwable
     */
    public function delete(Story $story): JsonResponse
    {
        abort_if($story->user_id != Auth::id(), 403, 'Permission denied');

        $story->delete();

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully deleted the story',
        ]);
    }

    /**
     * My stories list
     */
    public function myStories(): AnonymousResourceCollection
    {
        /** @var User $user */
        $user = Auth::user();
        $stories = $user->stories()
            ->with(['post.media', 'user.profile.media'])
            ->where('valid_until', '>', now())
            ->where('is_active', true)
            ->whereNull('blocked_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return StoryResource::customCollection($stories, []);
    }

    /**
     * User stories list
     */
    public function userStories(User $user): AnonymousResourceCollection
    {
        $viewedStoryIds = $this->getUserViewedStoryIds();
        $favoriteStoryIds = $this->getUserFavoriteStoryIds();

        $stories = $user->stories()
            ->with(['post.media', 'user.profile.media'])
            ->where('valid_until', '>', now())
            ->where('is_active', true)
            ->whereNull('blocked_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return StoryResource::customCollection($stories, [
            'viewedStoryIds' => $viewedStoryIds,
            'favoriteStoryIds' => $favoriteStoryIds,
        ]);
    }

    /**
     * Following users stories list
     */
    public function followingStories(): AnonymousResourceCollection
    {
        $user = Auth::user();

        $viewedStoryIds = $this->getUserViewedStoryIds();
        $favoriteStoryIds = $this->getUserFavoriteStoryIds();

        $followings = $user->followings()
            ->whereHas('stories', function ($query) {
                $query->where('valid_until', '>', now())
                    ->where('is_active', true)->whereNull('blocked_at');
            })
            ->with(['stories' => function ($query) {
                $query->where('valid_until', '>', now())
                    ->where('is_active', true)->whereNull('blocked_at')
                    ->orderBy('created_at', 'desc');
            }, 'stories.post.media'])
            ->get();

        $followingsStories = $followings->sort(function (User $a, User $b) use ($viewedStoryIds) {
            $aUnviewed = $a->stories->first(fn ($story) => ! in_array($story->id, $viewedStoryIds)) ? 0 : 1;
            $bUnviewed = $b->stories->first(fn ($story) => ! in_array($story->id, $viewedStoryIds)) ? 0 : 1;

            if ($aUnviewed === $bUnviewed) {
                $aRecentStory = $a->stories->first() ? $a->stories->first()->created_at : null;
                $bRecentStory = $b->stories->first() ? $b->stories->first()->created_at : null;

                return $bRecentStory <=> $aRecentStory;
            }

            return $aUnviewed <=> $bUnviewed;
        });

        return UserStoryResource::customCollection($followingsStories, [
            'viewedStoryIds' => $viewedStoryIds,
            'favoriteStoryIds' => $favoriteStoryIds,
        ]);
    }
}
