<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class ReferralService
{
    const TRANSACTION_SOURCE = 'referral';

    /**
     * Process referral when a new user signs up with a referral code.
     *
     * @param User $newUser
     * @param string $referralCode
     * @return array|null
     * @throws Exception
     */
    public function processReferral(User $newUser, string $referralCode): ?array
    {
        return DB::transaction(function () use ($newUser, $referralCode) {
            // Find referrer by code
            $referrer = User::where('referral_code', $referralCode)
                ->where('is_active', true)
                ->first();

            if (!$referrer) {
                throw new Exception('Invalid referral code');
            }

            // Prevent self-referral
            if ($referrer->id === $newUser->id) {
                throw new Exception('You cannot refer yourself');
            }

            // Check if user already has a referrer
            if ($newUser->referred_by) {
                throw new Exception('This user already has a referrer');
            }

            // Set the referrer
            $newUser->referred_by = $referrer->id;
            $newUser->save();

            // Get referral reward from settings
            $rewardAmount = AppSetting::get('referral_reward', 10);

            // Create referral record
            $referral = Referral::create([
                'referrer_id' => $referrer->id,
                'referee_id' => $newUser->id,
                'reward_amount' => $rewardAmount,
                'reward_claimed' => true,
                'reward_claimed_at' => now(),
                'status' => 'completed',
            ]);

            // Update referrer's balance
            $balanceBefore = $referrer->balance_tnt;
            $referrer->balance_tnt += $rewardAmount;
            $referrer->save();

            // Create transaction for referrer
            Transaction::create([
                'user_id' => $referrer->id,
                'type' => 'earn',
                'source' => self::TRANSACTION_SOURCE,
                'amount' => $rewardAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $referrer->balance_tnt,
                'description' => "Referral bonus - {$newUser->username} joined using your code",
                'metadata' => [
                    'referee_id' => $newUser->id,
                    'referee_username' => $newUser->username,
                    'referral_code' => $referralCode,
                ],
            ]);

            return [
                'referrer' => $referrer,
                'referee' => $newUser,
                'reward_amount' => (float) $rewardAmount,
                'referral_id' => $referral->id,
            ];
        });
    }

    /**
     * Get user's referral information.
     *
     * @param User $user
     * @return array
     */
    public function getReferralInfo(User $user): array
    {
        // Ensure user has a referral code
        if (!$user->referral_code) {
            $user->referral_code = User::generateReferralCode();
            $user->save();
        }

        $rewardAmount = AppSetting::get('referral_reward', 10);
        
        // Get referral statistics
        $totalReferrals = $user->referrals()->count();
        $successfulReferrals = $user->referrals()->where('status', 'completed')->count();
        $totalEarned = $user->referrals()
            ->where('status', 'completed')
            ->sum('reward_amount');

        // Get list of referrals
        $referrals = $user->referrals()
            ->with('referee:id,username,created_at')
            ->latest()
            ->get()
            ->map(function ($referral) {
                return [
                    'id' => $referral->id,
                    'referee_username' => $referral->referee->username,
                    'reward_amount' => (float) $referral->reward_amount,
                    'status' => $referral->status,
                    'joined_at' => $referral->created_at->toDateTimeString(),
                ];
            });

        return [
            'referral_code' => $user->referral_code,
            'reward_per_referral' => (float) $rewardAmount,
            'total_referrals' => $totalReferrals,
            'successful_referrals' => $successfulReferrals,
            'total_earned' => (float) $totalEarned,
            'referrals' => $referrals,
        ];
    }

    /**
     * Validate referral code.
     *
     * @param string $referralCode
     * @return bool
     */
    public function validateReferralCode(string $referralCode): bool
    {
        return User::where('referral_code', $referralCode)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get referral statistics for admin.
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $totalReferrals = Referral::count();
        $completedReferrals = Referral::where('status', 'completed')->count();
        $totalRewardsDistributed = Referral::where('status', 'completed')->sum('reward_amount');
        
        // Get top referrers by counting completed referrals
        $topReferrers = DB::table('referrals')
            ->join('users', 'referrals.referrer_id', '=', 'users.id')
            ->where('referrals.status', 'completed')
            ->select('users.username', DB::raw('COUNT(*) as referral_count'))
            ->groupBy('referrals.referrer_id', 'users.username')
            ->orderByDesc('referral_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'username' => $item->username,
                    'referral_count' => (int) $item->referral_count,
                ];
            });

        return [
            'total_referrals' => $totalReferrals,
            'completed_referrals' => $completedReferrals,
            'total_rewards_distributed' => (float) $totalRewardsDistributed,
            'conversion_rate' => $totalReferrals > 0 
                ? round(($completedReferrals / $totalReferrals) * 100, 2) 
                : 0,
            'top_referrers' => $topReferrers,
        ];
    }
}

