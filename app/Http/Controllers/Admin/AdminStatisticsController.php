<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminStatisticsController extends Controller
{
    /**
     * Get statistics
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        // Validate period input
        $request->validate([
            'period' => 'required|in:7d,1m,3m,6m,1y,all',
        ]);

        $period = $request->input('period');
        $periods = $this->getPeriods($period);

        // User Statistics
        $allUsersStats = $this->getStatistics($periods, User::class);
        $userStats = $this->getStatistics($periods, User::class, 'type', User::TYPE_USER);
        $sellerStats = $this->getStatistics($periods, User::class, 'type', User::TYPE_SELLER);

        // Post Statistics
        $allPostsStats = $this->getStatistics($periods, Post::class);
        $postStats = $this->getStatistics($periods, Post::class, 'type', 'post');
        $productStats = $this->getStatistics($periods, Post::class, 'type', 'product');

        return response()->json([
            'all_users' => $allUsersStats,
            'standard_users' => $userStats,
            'seller_users' => $sellerStats,
            'all_posts' => $allPostsStats,
            'standard_posts' => $postStats,
            'product_posts' => $productStats,
        ]);
    }


    private function getStatistics(array $periods, string $modelClass, ?string $typeField = null, ?string $typeValue = null): array
    {
        $stats = [];

        foreach ($periods as $period) {
            $query = $modelClass::query();

            if ($typeField && $typeValue) {
                $query->where($typeField, $typeValue);
            }

            $count = $query->whereBetween('created_at', [$period['start'], $period['end']])->count();
            $stats[] = $count;
        }

        // Limit to last 5 periods only
        $last5Stats = array_slice($stats, -5);
        $last5Periods = array_slice($periods, -5);

        return $this->calculateGrowth($last5Stats, $last5Periods);
    }

    private function calculateGrowth(array $stats, array $periods): array
    {
        $growthData = [];

        for ($i = 0; $i < count($stats); $i++) {
            $previousCount = $i === 0 ? 0 : $stats[$i - 1];
            $currentCount = $stats[$i];

            // Calculate growth only if the previous count is not zero to avoid division by zero error
            $growth = $previousCount > 0 ? (($currentCount - $previousCount) / $previousCount) * 100 : 0;

            $growthData[] = [
                'count' => $currentCount,
                'growth' => round($growth, 2),
                'start_date' => $periods[$i]['start']->format('Y-m-d'),
                'end_date' => $periods[$i]['end']->format('Y-m-d'),
            ];
        }

        return $growthData;
    }

    private function getPeriods(string $period): array
    {
        return match ($period) {
            '7d' => $this->generateBackwardPeriods('days', 7, 5),
            '1m' => $this->generateBackwardPeriods('months', 1, 5),
            '3m' => $this->generateBackwardPeriods('months', 3, 5),
            '6m' => $this->generateBackwardPeriods('months', 6, 5),
            '1y' => $this->generateBackwardPeriods('years', 1, 5),
            'all' => $this->generateAllPeriod(),
            default => [],
        };
    }

    private function generateBackwardPeriods(string $unit, int $interval, int $count): array
    {
        $periods = [];
        $currentEnd = now()->endOfDay();

        for ($i = 0; $i < $count; $i++) {
            $currentStart = $currentEnd->copy()->sub($unit, $interval)->addSecond();
            $periods[] = [
                'start' => $currentStart,
                'end' => $currentEnd,
            ];
            $currentEnd = $currentStart->copy()->subSecond();
        }

        return array_reverse($periods);
    }

    private function generateAllPeriod(): array
    {
        $earliestUser = User::orderBy('created_at', 'asc')->first();
        $start = $earliestUser ? Carbon::parse($earliestUser->created_at)->startOfDay() : now()->startOfDay();
        $end = now()->endOfDay();

        return [
            ['start' => $start, 'end' => $end]
        ];
    }

}

