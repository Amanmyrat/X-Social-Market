<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Statistics\ActiveUsersStatisticsResource;
use App\Http\Resources\Statistics\FollowerStatisticsResource;
use App\Http\Resources\Statistics\PostsStatisticsResource;
use App\Http\Resources\Statistics\PostStatisticsResource;
use App\Http\Resources\Statistics\ProfileViewStatisticsResource;
use App\Http\Resources\Statistics\TopActiveUsersStatisticsResource;
use App\Http\Resources\Statistics\UserStatisticsResource;
use App\Models\Post;
use App\Services\Statistics\PostStatisticsService;
use App\Services\Statistics\ProfileViewStatisticsService;
use App\Services\Statistics\UserEngagementStatisticsService;
use App\Services\Statistics\UserFollowerStatisticsService;
use App\Services\Statistics\UserStatisticsService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserStatisticsController
{
    public function __construct(
        protected UserStatisticsService $statisticsService,
        protected ProfileViewStatisticsService $profileViewStatisticsService,
        protected UserEngagementStatisticsService $userEngagementStatisticsService,
        protected PostStatisticsService $postStatisticsService,
        protected UserFollowerStatisticsService $followerStatisticsService,
    ) {

    }

    /**
     * User statistics
     */
    public function statistics(Request $request): UserStatisticsResource
    {
        $request->validate([
            'period' => 'required|in:1d,10d,1m,6m,1y,all',
        ]);

        $statistics = $this->statisticsService->getWithPrevious($request->period);

        return new UserStatisticsResource($statistics);
    }

    /**
     * User profile view statistics
     */
    public function profileViewStatistics(Request $request): ProfileViewStatisticsResource
    {
        $request->validate([
            'period' => 'required|in:1d,10d,1m,6m,1y,all',
        ]);

        $statistics = $this->profileViewStatisticsService->get($request->period);

        return new ProfileViewStatisticsResource($statistics);
    }

    /**
     * Active users statistics
     */
    public function activeUsersStatistics(Request $request): ActiveUsersStatisticsResource
    {
        $request->validate([
            'period' => 'required|in:1d,10d,1m,6m,1y,all',
        ]);

        $statistics = $this->userEngagementStatisticsService->get(Auth::id(), $request->period);

        return new ActiveUsersStatisticsResource($statistics);
    }

    /**
     * Active top users statistics
     */
    public function topActiveUsersStatistics(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'period' => 'required|in:1d,10d,1m,6m,1y,all',
            'top' => 'required|integer',
        ]);

        $statistics = $this->userEngagementStatisticsService->getTopActiveUsers(
            Auth::id(),
            $request->top,
            $request->period
        );

        return TopActiveUsersStatisticsResource::collection($statistics);
    }

    /**
     * Posts statistics
     */
    public function postsStatistics(Request $request): PostsStatisticsResource
    {
        $request->validate([
            'period' => 'required|in:1d,10d,1m,6m,1y,all',
        ]);

        $statistics = $this->postStatisticsService->get(
            Auth::id(),
            $request->period
        );

        return new PostsStatisticsResource($statistics);
    }

    /**
     * One Post's statistics
     */
    public function onePostsStatistics(Post $post, Request $request): PostStatisticsResource
    {
        $request->validate([
            'period' => 'required|in:1d,10d,1m,6m,1y,all',
        ]);

        $statistics = $this->postStatisticsService->getPostStatistics(
            $post->id,
            Auth::id(),
            $request->period
        );

        return new PostStatisticsResource($statistics);
    }

    /**
     * Followers statistics
     */
    public function followersStatistics(Request $request): FollowerStatisticsResource
    {
        $request->validate([
            'period' => 'required|in:1d,10d,1m,6m,1y,all',
        ]);

        $statistics = $this->followerStatisticsService->get(
            Auth::id(),
            $request->period
        );

        return new FollowerStatisticsResource($statistics);
    }
}
