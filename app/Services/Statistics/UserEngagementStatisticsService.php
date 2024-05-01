<?php

namespace App\Services\Statistics;

use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;

class UserEngagementStatisticsService extends BaseStatisticsService
{
    public function get($userId, $period): array
    {
        $startDate = $this->getStartDateForPeriod($period);
        $previousStartDate = $this->getPreviousStartDateForPeriod($period, $startDate);
        $endDate = now();

        $currentUniqueUserIds = $this->getUniqueEngagedUserIds($userId, $startDate, $endDate);
        // Fetch engagement user IDs for the previous period
        $previousUniqueUserIds = $this->getUniqueEngagedUserIds($userId, $previousStartDate, $startDate);

        // Calculate distributions and changes
        return [
            'total_engagements' => count($currentUniqueUserIds),
            'total_engagements_previous' => count($previousUniqueUserIds),
            'total_engagements_change' => $this->calculatePercentageChange(count($previousUniqueUserIds), count($currentUniqueUserIds)),
            'followers_distribution' => $this->getFollowersDistribution($currentUniqueUserIds, $previousUniqueUserIds, $userId),
            'gender_distribution' => $this->getGenderDistribution($currentUniqueUserIds),
            'age_distribution' => $this->getAgeDistribution($currentUniqueUserIds),
        ];
    }

    protected function getUniqueEngagedUserIds($userId, $startDate, $endDate): array
    {
        // Use a single query to gather all unique user IDs from engagements within the date range.
        $engagements = DB::table('posts')
            ->where('posts.user_id', $userId)
            ->leftJoin('post_favorites', 'posts.id', '=', 'post_favorites.post_id')
            ->leftJoin('post_comments', 'posts.id', '=', 'post_comments.post_id')
            ->leftJoin('post_bookmarks', 'posts.id', '=', 'post_bookmarks.post_id')
            ->leftJoin('post_ratings', 'posts.id', '=', 'post_ratings.post_id')
            ->select('post_favorites.user_id as favorite_user_id', 'post_comments.user_id as comment_user_id', 'post_bookmarks.user_id as bookmark_user_id', 'post_ratings.user_id as rating_user_id')
            ->when($startDate, function ($query) use ($startDate) {
                $query->where(function ($q) use ($startDate) {
                    $q->where('post_favorites.created_at', '>=', $startDate)
                        ->orWhere('post_comments.created_at', '>=', $startDate)
                        ->orWhere('post_bookmarks.created_at', '>=', $startDate)
                        ->orWhere('post_ratings.created_at', '>=', $startDate);
                });
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->where(function ($q) use ($endDate) {
                    $q->where('post_favorites.created_at', '<', $endDate)
                        ->orWhere('post_comments.created_at', '<', $endDate)
                        ->orWhere('post_bookmarks.created_at', '<', $endDate)
                        ->orWhere('post_ratings.created_at', '<', $endDate);
                });
            })
            ->get();

        // Flatten the list of user IDs from different columns and remove null values
        return $engagements->flatMap(function ($item) {
            return [$item->favorite_user_id, $item->comment_user_id, $item->bookmark_user_id, $item->rating_user_id];
        })->filter()->unique()->toArray();

    }

    protected function getFollowersDistribution($currentUniqueUserIds, $previousUniqueUserIds, $userId): array
    {
        $followers = User::find($userId)->followers()->pluck('users.id')->toArray();

        // Calculate current period distribution
        $currentFollowerCount = count(array_intersect($followers, $currentUniqueUserIds));
        $currentNonFollowerCount = count($currentUniqueUserIds) - $currentFollowerCount;
        $currentTotalCount = count($currentUniqueUserIds);

        // Calculate previous period distribution
        $previousFollowerCount = count(array_intersect($followers, $previousUniqueUserIds));
        $previousNonFollowerCount = count($previousUniqueUserIds) - $previousFollowerCount;
        $previousTotalCount = count($previousUniqueUserIds);

        // Calculate percentages
        $currentFollowersPercentage = $currentTotalCount ? round($currentFollowerCount / $currentTotalCount * 100, 2) : 0;
        $previousFollowersPercentage = $previousTotalCount ? round($previousFollowerCount / $previousTotalCount * 100, 2) : 0;

        $currentNonFollowersPercentage = $currentTotalCount ? round($currentNonFollowerCount / $currentTotalCount * 100, 2) : 0;
        $previousNonFollowersPercentage = $previousTotalCount ? round($previousNonFollowerCount / $previousTotalCount * 100, 2) : 0;

        return [
            'followers' => $currentFollowersPercentage,
            'followers_previous' => $previousFollowersPercentage,
            'non_followers' => $currentNonFollowersPercentage,
            'non_followers_previous' => $previousNonFollowersPercentage
        ];
    }

    protected function getGenderDistribution($userIds): array
    {
        $users = User::whereIn('id', $userIds)->with('profile')->get();
        $genderCounts = ['male' => 0, 'female' => 0, 'undefined' => 0];

        foreach ($users as $user) {
            $gender = $user->profile ? strtolower($user->profile->gender ?? 'undefined') : 'undefined';
            if (! in_array($gender, ['male', 'female'])) {
                $gender = 'undefined';
            }
            $genderCounts[$gender]++;
        }

        $totalCount = array_sum($genderCounts);

        return $totalCount ? array_map(function ($count) use ($totalCount) {
            return round($count / $totalCount * 100, 2);
        }, $genderCounts) : $genderCounts;
    }

    protected function getAgeDistribution($userIds): array
    {
        $users = User::whereIn('id', $userIds)->with('profile')->get();
        $now = Carbon::now();

        $ageRanges = [
            '13-17' => 0,
            '18-24' => 0,
            '25-34' => 0,
            '35-44' => 0,
            '45-64' => 0,
            '65+' => 0,
            'undefined' => 0,
        ];

        foreach ($users as $user) {
            $birthday = optional($user->profile)->birthdate;
            $age = $birthday ? $now->diffInYears($birthday) : 'undefined';
            $ageRange = $this->calculateAgeRange($age);
            $ageRanges[$ageRange]++;
        }

        $totalCount = array_sum($ageRanges);

        return $totalCount ? array_map(function ($count) use ($totalCount) {
            return round(($count / $totalCount) * 100, 2);
        }, $ageRanges) : $ageRanges;
    }

    public function getTopActiveUsers($userId, $topN, $period)
    {
        $startDate = $this->getStartDateForPeriod($period);
        $postIds = Post::where('user_id', $userId)->pluck('id');

        $engagements = [
            'favorites' => $this->aggregateEngagements('post_favorites', $postIds, $startDate),
            'comments' => $this->aggregateEngagements('post_comments', $postIds, $startDate),
            'bookmarks' => $this->aggregateEngagements('post_bookmarks', $postIds, $startDate),
            'ratings' => $this->aggregateEngagements('post_ratings', $postIds, $startDate),
        ];

        $totalCountsPerUser = collect($engagements)->flatMap(fn ($e) => $e)->groupBy('user_id')
            ->map(fn ($actions) => $actions->sum('count'))
            ->sortDesc()
            ->take($topN);

        $users = User::whereIn('id', $totalCountsPerUser->keys())->with('profile.media')->get();

        $detailedResults = $users->map(function ($user) use ($engagements) {
            $userEngagements = [
                'favorites' => $engagements['favorites']->where('user_id', $user->id)->sum('count') ?? 0,
                'comments' => $engagements['comments']->where('user_id', $user->id)->sum('count') ?? 0,
                'bookmarks' => $engagements['bookmarks']->where('user_id', $user->id)->sum('count') ?? 0,
                'ratings' => $engagements['ratings']->where('user_id', $user->id)->sum('count') ?? 0,
            ];

            return [
                'user' => $user,
                'details' => $userEngagements,
                'total_engagements' => array_sum($userEngagements),
            ];
        });

        return $detailedResults->sortByDesc('total_engagements')->values()->all();
    }

    protected function aggregateEngagements($table, $postIds, $startDate): Collection
    {
        return DB::table($table)
            ->select('user_id', DB::raw('COUNT(*) as count'))
            ->whereIn('post_id', $postIds)
            ->when($startDate, function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');
    }
}
