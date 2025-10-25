<?php

namespace App\Filament\Widgets;

use App\Services\DailyLoginService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DailyLoginStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $service = app(DailyLoginService::class);
        $stats = $service->getStatistics();

        return [
            Stat::make('Daily Active Users', $stats['daily_active_users'])
                ->description('Users who claimed today')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            
            Stat::make('Weekly Active Users', $stats['weekly_active_users'])
                ->description('Users active in last 7 days')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            
            Stat::make('Total TNT Distributed', number_format($stats['total_tnt_distributed'], 2))
                ->description('All-time distribution')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
            
            Stat::make('Monthly Distribution', number_format($stats['monthly_tnt_distributed'], 2))
                ->description('This month\'s distribution')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
            
            Stat::make('Average Streak', number_format($stats['average_streak'], 2) . ' days')
                ->description('User engagement level')
                ->descriptionIcon('heroicon-m-fire')
                ->color('success'),
            
            Stat::make('Highest Streak Record', $stats['highest_streak_record'] . ' days')
                ->description('Best streak achieved')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('danger'),
        ];
    }
}

