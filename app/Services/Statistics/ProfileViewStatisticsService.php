<?php

namespace App\Services\Statistics;

use App\Models\ProfileView;
use Auth;
use Illuminate\Database\Eloquent\Collection;

class ProfileViewStatisticsService extends BaseStatisticsService
{
    public function get(string $period): array
    {
        $startDate = $this->getStartDateForPeriod($period);
        $previousStartDate = $this->getPreviousStartDateForPeriod($period, $startDate);
        $endDate = now();

        $currentProfileViews = $this->fetchProfileViews($startDate, $endDate);
        $previousProfileViews = $this->fetchProfileViews($previousStartDate, $startDate);

        return [
            'total_views' => $currentProfileViews->count(),
            'total_views_previous' => $previousProfileViews->count(),
            'total_views_change' => $this->calculatePercentageChange($previousProfileViews->count(), $currentProfileViews->count()),
            'followers_distribution' => $this->getFollowerDistribution($currentProfileViews, $previousProfileViews),
            'gender_distribution' => $this->getGenderDistribution($currentProfileViews),
            'age_distribution' => $this->getAgeDistribution($currentProfileViews),
        ];
    }

    private function fetchProfileViews($startDate, $endDate): Collection
    {
        return ProfileView::where('user_profile_id', Auth::user()->profile->id)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<', $endDate)
            ->with(['viewer.profile'])
            ->get();
    }

    private function getFollowerDistribution($currentViews, $previousViews): array
    {
        $followerIds = Auth::user()->followers()->pluck('users.id')->toArray();

        $currentFollowerViews = $currentViews->whereIn('viewer_id', $followerIds)->count();
        $currentNonFollowerViews = $currentViews->whereNotIn('viewer_id', $followerIds)->count();

        $previousFollowerViews = $previousViews->whereIn('viewer_id', $followerIds)->count();
        $previousNonFollowerViews = $previousViews->whereNotIn('viewer_id', $followerIds)->count();

        $totalCurrentViews = $currentViews->count();
        $totalPreviousViews = $previousViews->count();

        // Calculate current and previous percentages
        $currentFollowerPercentage = $totalCurrentViews > 0 ? round(($currentFollowerViews / $totalCurrentViews) * 100, 2) : 0;
        $previousFollowerPercentage = $totalPreviousViews > 0 ? round(($previousFollowerViews / $totalPreviousViews) * 100, 2) : 0;

        $currentNonFollowerPercentage = $totalCurrentViews > 0 ? round(($currentNonFollowerViews / $totalCurrentViews) * 100, 2) : 0;
        $previousNonFollowerPercentage = $totalPreviousViews > 0 ? round(($previousNonFollowerViews / $totalPreviousViews) * 100, 2) : 0;

        return [
            'followers' => $currentFollowerPercentage,
            'followers_previous' => $previousFollowerPercentage,
            'non_followers' => $currentNonFollowerPercentage,
            'non_followers_previous' => $previousNonFollowerPercentage,
        ];
    }

    private function getGenderDistribution($profileViews): array
    {
        $genderCounts = [
            'male' => 0,
            'female' => 0,
            'undefined' => 0,
        ];

        foreach ($profileViews as $view) {
            $gender = optional($view->viewer->profile)->gender ?? 'undefined';
            $gender = strtolower($gender);
            if (! in_array($gender, ['male', 'female'])) {
                $gender = 'undefined';
            }
            $genderCounts[$gender]++;
        }

        $totalViews = $profileViews->count();

        return $totalViews > 0 ? array_map(function ($count) use ($totalViews) {
            return round(($count / $totalViews) * 100, 2);
        }, $genderCounts) : $genderCounts;
    }

    private function getAgeDistribution($profileViews): array
    {
        $ageRanges = [
            '13-17' => 0,
            '18-24' => 0,
            '25-34' => 0,
            '35-44' => 0,
            '45-64' => 0,
            '65+' => 0,
            'undefined' => 0,
        ];

        foreach ($profileViews as $view) {
            $birthday = optional($view->viewer->profile)->birthdate;
            $age = $birthday ? now()->diffInYears($birthday) : 'undefined';
            $ageRange = $this->calculateAgeRange($age);
            $ageRanges[$ageRange]++;
        }

        $totalViews = $profileViews->count();

        return $totalViews > 0 ? array_map(function ($count) use ($totalViews) {
            return round(($count / $totalViews) * 100, 2);
        }, $ageRanges) : $ageRanges;
    }
}
