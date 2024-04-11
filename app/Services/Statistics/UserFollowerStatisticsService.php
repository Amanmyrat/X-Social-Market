<?php

namespace App\Services\Statistics;

use App\Models\Follower;
use App\Models\User;
use Carbon\Carbon;

class UserFollowerStatisticsService extends BaseStatisticsService
{
    public function get($userId, $period): array
    {
        $startDate = $this->getStartDateForPeriod($period);

        $newFollowersUserIds = $this->getNewFollowersUserIds($userId, $startDate);
        $newUnfollowersUserIds = $this->getNewUnfollowersUserIds($userId, $startDate);

        return [
            'new_followers_count' => count($newFollowersUserIds),
            'new_unfollowers_count' => count($newUnfollowersUserIds),
            'gender_distribution_new_followers' => $this->getGenderDistribution($newFollowersUserIds),
            'age_distribution_new_followers' => $this->getAgeDistribution($newFollowersUserIds),
        ];
    }

    protected function getNewFollowersUserIds($userId, $startDate): array
    {
        $query = Follower::where('followed_user_id', $userId)
            ->whereNull('unfollowed_at');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        return $query->pluck('following_user_id')->toArray();
    }

    protected function getNewUnfollowersUserIds($userId, $startDate): array
    {
        $query = Follower::where('followed_user_id', $userId)
            ->whereNotNull('unfollowed_at');

        if ($startDate) {
            $query->where('unfollowed_at', '>=', $startDate);
        }

        return $query->pluck('following_user_id')->toArray();
    }

    protected function getGenderDistribution(array $userIds): array
    {
        if (empty($userIds)) {
            return ['male' => 0, 'female' => 0, 'undefined' => 100];
        }

        $users = User::whereIn('id', $userIds)->with('profile')->get();
        $genderCounts = ['male' => 0, 'female' => 0, 'undefined' => 0];

        foreach ($users as $user) {
            $gender = optional($user->profile)->gender ?? 'undefined';
            $genderCounts[strtolower($gender)]++;
        }

        $totalCount = array_sum($genderCounts);

        return array_map(function ($count) use ($totalCount) {
            return round(($count / $totalCount) * 100, 2);
        }, $genderCounts);
    }

    protected function getAgeDistribution(array $userIds): array
    {
        if (empty($userIds)) {
            return ['undefined' => 100];
        }

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

        return array_map(function ($count) use ($totalCount) {
            return round(($count / $totalCount) * 100, 2);
        }, $ageRanges);
    }
}
