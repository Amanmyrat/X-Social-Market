<?php

namespace App\Filament\Widgets;

use App\Services\ReferralService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReferralStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $service = app(ReferralService::class);
        $stats = $service->getStatistics();

        return [
            Stat::make('Total Referrals', $stats['total_referrals'])
                ->description('All referral attempts')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            
            Stat::make('Completed Referrals', $stats['completed_referrals'])
                ->description('Successfully rewarded')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Total Rewards Distributed', number_format($stats['total_rewards_distributed'], 2) . ' TNT')
                ->description('Total TNT given via referrals')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
            
            Stat::make('Conversion Rate', $stats['conversion_rate'] . '%')
                ->description('Successful completion rate')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
        ];
    }
}

