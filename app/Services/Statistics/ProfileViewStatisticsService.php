<?php

namespace App\Services\Statistics;

use App\Http\Resources\Statistics\ProfileViewStatisticsResource;
use App\Models\ProfileView;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ProfileViewStatisticsService extends BaseStatisticsService
{
    public function get(string $period): array
    {
        $startDate = $this->getStartDateForPeriod($period);

        $profileViews = ProfileView::where('user_profile_id',Auth::user()->profile->id)
            ->when($startDate, function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->with(['viewer.profile'])
            ->get();

        return [
            'total_views' => $profileViews->count(),
            'followers_distribution' => $this->getFollowerDistribution($profileViews),
            'gender_distribution' => $this->getGenderDistribution($profileViews),
            'age_distribution' => $this->getAgeDistribution($profileViews),
        ];
    }

    private function calculateAgeRange($age): string
    {
        if ($age === 'undefined') return $age;
        if ($age <= 17) return '13-17';
        if ($age <= 24) return '18-24';
        if ($age <= 34) return '25-34';
        if ($age <= 44) return '35-44';
        if ($age <= 64) return '45-64';
        return '65+';
    }

    private function getFollowerDistribution($profileViews): array
    {
        $followerIds = Auth::user()->followers()->pluck('users.id')->toArray();
        $followerViews = $profileViews->whereIn('viewer_id', $followerIds)->count();
        $nonFollowerViews = $profileViews->count() - $followerViews;

        $totalViews = $profileViews->count();
        return [
            'followers' => $totalViews > 0 ? round(($followerViews / $totalViews) * 100, 2) : 0,
            'non_followers' => $totalViews > 0 ? round(($nonFollowerViews / $totalViews) * 100, 2) : 0,
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
            if (!in_array($gender, ['male', 'female'])) {
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
        // Define age ranges
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
