<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Statistics\ProfileViewStatisticsResource;
use App\Services\Statistics\ProfileViewStatisticsService;
use App\Services\Statistics\UserStatisticsService;
use Illuminate\Http\Request;
use App\Http\Resources\Statistics\UserStatisticsResource;

class UserStatisticsController
{
    public function __construct(
        protected UserStatisticsService        $statisticsService,
        protected ProfileViewStatisticsService $profileViewStatisticsService)
    {

    }

    /**
     * User statistics
     */
    public function statistics(Request $request): UserStatisticsResource
    {
        $request->validate([
            'period' => 'required|in:1d,10d,1m,6m,1y,all',
        ]);

        $statistics = $this->statisticsService->get($request->period);

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
}
