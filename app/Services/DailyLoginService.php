<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\DailyLoginReward;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class DailyLoginService
{
    const MAX_STREAK_DAYS = 7;
    const TRANSACTION_SOURCE = 'daily_login';

    /**
     * Claim daily login reward for a user.
     *
     * @param User $user
     * @return array
     * @throws Exception
     */
    public function claimDailyReward(User $user): array
    {
        return DB::transaction(function () use ($user) {
            // Get or create daily login reward record
            $dailyReward = DailyLoginReward::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'current_streak' => 0,
                    'highest_streak' => 0,
                    'last_login_date' => null,
                    'total_earned' => 0,
                    'total_claims' => 0,
                ]
            );

            $today = Carbon::today();
            $lastLoginDate = $dailyReward->last_login_date;

            // Check if already claimed today
            if ($lastLoginDate && $lastLoginDate->isSameDay($today)) {
                throw new Exception('You have already claimed your daily reward today. Come back tomorrow!');
            }

            // Calculate new streak
            $newStreak = $this->calculateStreak($lastLoginDate, $today, $dailyReward->current_streak);

            // Get base reward from settings
            $baseReward = AppSetting::get('daily_login_base_reward', 2);

            // Calculate actual reward amount
            $rewardAmount = $baseReward * $newStreak;

            // Get user's current balance before update
            $balanceBefore = $user->balance_tnt;

            // Update user balance
            $user->balance_tnt = $balanceBefore + $rewardAmount;
            $user->save();

            // Update daily login reward record
            $dailyReward->current_streak = $newStreak;
            $dailyReward->highest_streak = max($dailyReward->highest_streak, $newStreak);
            $dailyReward->last_login_date = $today;
            $dailyReward->total_earned += $rewardAmount;
            $dailyReward->total_claims += 1;
            $dailyReward->save();

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'earn',
                'source' => self::TRANSACTION_SOURCE,
                'amount' => $rewardAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $user->balance_tnt,
                'description' => $this->getRewardDescription($newStreak),
                'metadata' => [
                    'streak' => $newStreak,
                    'base_reward' => $baseReward,
                    'claim_date' => $today->toDateString(),
                ],
            ]);

            // Calculate next reward preview
            $nextStreak = $newStreak >= self::MAX_STREAK_DAYS ? 1 : $newStreak + 1;
            $nextReward = $baseReward * $nextStreak;

            return [
                'success' => true,
                'reward' => (float) $rewardAmount,
                'current_streak' => $newStreak,
                'next_reward_preview' => (float) $nextReward,
                'new_balance' => (float) $user->balance_tnt,
                'message' => $this->getRewardMessage($newStreak),
                'transaction_id' => $transaction->id,
            ];
        });
    }

    /**
     * Get daily login status for a user.
     *
     * @param User $user
     * @return array
     */
    public function getDailyLoginStatus(User $user): array
    {
        $dailyReward = DailyLoginReward::where('user_id', $user->id)->first();
        $baseReward = AppSetting::get('daily_login_base_reward', 2);

        if (!$dailyReward) {
            return [
                'current_streak' => 0,
                'last_login' => null,
                'next_reward' => (float) $baseReward,
                'base_reward' => (float) $baseReward,
                'can_claim_today' => true,
                'total_earned' => 0,
                'total_claims' => 0,
                'highest_streak' => 0,
            ];
        }

        $today = Carbon::today();
        $canClaimToday = !$dailyReward->last_login_date || !$dailyReward->last_login_date->isSameDay($today);

        // Calculate what the next streak would be
        $nextStreak = $canClaimToday
            ? $this->calculateStreak($dailyReward->last_login_date, $today, $dailyReward->current_streak)
            : ($dailyReward->current_streak >= self::MAX_STREAK_DAYS ? 1 : $dailyReward->current_streak + 1);

        $nextReward = $baseReward * $nextStreak;

        return [
            'current_streak' => $dailyReward->current_streak,
            'last_login' => $dailyReward->last_login_date?->toDateString(),
            'next_reward' => (float) $nextReward,
            'base_reward' => (float) $baseReward,
            'can_claim_today' => $canClaimToday,
            'total_earned' => (float) $dailyReward->total_earned,
            'total_claims' => $dailyReward->total_claims,
            'highest_streak' => $dailyReward->highest_streak,
        ];
    }

    /**
     * Calculate the new streak based on last login date.
     *
     * @param Carbon|null $lastLoginDate
     * @param Carbon $today
     * @param int $currentStreak
     * @return int
     */
    protected function calculateStreak(?Carbon $lastLoginDate, Carbon $today, int $currentStreak): int
    {
        // First time login
        if (!$lastLoginDate) {
            return 1;
        }

        // Calculate days difference
        $daysDiff = $lastLoginDate->diffInDays($today);

        // Same day (shouldn't happen due to earlier check, but safety)
        if ($daysDiff === 0) {
            return $currentStreak;
        }

        // Yesterday - continue streak
        if ($daysDiff === 1) {
            $newStreak = $currentStreak + 1;
            // Reset to 1 if reached max streak
            return $newStreak > self::MAX_STREAK_DAYS ? 1 : $newStreak;
        }

        // Gap of more than 1 day - reset streak
        return 1;
    }

    /**
     * Get reward description for transaction.
     *
     * @param int $streak
     * @return string
     */
    protected function getRewardDescription(int $streak): string
    {
        return "Daily login bonus - Day {$streak}";
    }

    /**
     * Get reward message for user.
     *
     * @param int $streak
     * @return string
     */
    protected function getRewardMessage(int $streak): string
    {
        if ($streak === 1) {
            return "Welcome! You've claimed your daily reward!";
        }

        if ($streak === self::MAX_STREAK_DAYS) {
            return "Amazing! You've completed a {$streak}-day streak! The streak will restart tomorrow.";
        }

        return "Great! You're on a {$streak}-day streak! Keep it up!";
    }

    /**
     * Get statistics for admin dashboard.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $today = Carbon::today();

        // Users who claimed today
        $dailyActiveUsers = DailyLoginReward::whereDate('last_login_date', $today)->count();

        // Total TNT distributed via daily login
        $totalDistributed = Transaction::where('source', self::TRANSACTION_SOURCE)
            ->where('type', 'earn')
            ->sum('amount');

        // Average streak
        $averageStreak = DailyLoginReward::where('current_streak', '>', 0)
            ->avg('current_streak');

        // Highest streak record
        $highestStreak = DailyLoginReward::max('highest_streak');

        // Weekly active users (claimed in last 7 days)
        $weeklyActiveUsers = DailyLoginReward::where('last_login_date', '>=', $today->copy()->subDays(7))
            ->count();

        // Monthly distributed
        $monthlyDistributed = Transaction::where('source', self::TRANSACTION_SOURCE)
            ->where('type', 'earn')
            ->whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->sum('amount');

        return [
            'daily_active_users' => $dailyActiveUsers,
            'total_tnt_distributed' => (float) $totalDistributed,
            'average_streak' => round($averageStreak ?? 0, 2),
            'highest_streak_record' => $highestStreak ?? 0,
            'weekly_active_users' => $weeklyActiveUsers,
            'monthly_tnt_distributed' => (float) $monthlyDistributed,
        ];
    }
}

