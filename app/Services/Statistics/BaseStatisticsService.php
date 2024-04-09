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
}
