<?php

namespace App\Services\Statistics;

use Illuminate\Support\Carbon;

class BaseStatisticsService
{
    protected function getStartDateForPeriod($period): ?Carbon
    {
        return match ($period) {
            '1d' => now()->subDay(),
            '10d' => now()->subDays(10),
            '1m' => now()->subMonth(),
            '6m' => now()->subMonths(6),
            '1y' => now()->subYear(),
            'all' => null,
            default => now(),
        };
    }

    protected function getPreviousStartDateForPeriod(String $period, Carbon $currentStartDate): ?Carbon
    {
        return match ($period) {
            '1d' => $currentStartDate->subDay(),
            '10d' => $currentStartDate->subDays(10),
            '1m' => $currentStartDate->subMonth(),
            '6m' => $currentStartDate->subMonths(6),
            '1y' => $currentStartDate->subYear(),
            'all' => null,
            default => $currentStartDate,
        };
    }

    protected function calculateAgeRange($age): string
    {
        if ($age === 'undefined') {
            return $age;
        }
        if ($age <= 17) {
            return '13-17';
        }
        if ($age <= 24) {
            return '18-24';
        }
        if ($age <= 34) {
            return '25-34';
        }
        if ($age <= 44) {
            return '35-44';
        }
        if ($age <= 64) {
            return '45-64';
        }

        return '65+';
    }

    protected function calculatePercentageChange($previous, $current): float|int
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }
}
