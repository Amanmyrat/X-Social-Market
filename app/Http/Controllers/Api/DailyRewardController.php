<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DailyLoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class DailyRewardController extends Controller
{
    protected DailyLoginService $dailyLoginService;

    public function __construct(DailyLoginService $dailyLoginService)
    {
        $this->dailyLoginService = $dailyLoginService;
    }

    /**
     * Claim daily login reward.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function claimDailyReward(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $result = $this->dailyLoginService->claimDailyReward($user);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'reward' => $result['reward'],
                    'current_streak' => $result['current_streak'],
                    'next_reward_preview' => $result['next_reward_preview'],
                    'new_balance' => $result['new_balance'],
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get daily login status for the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDailyLoginStatus(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $status = $this->dailyLoginService->getDailyLoginStatus($user);

            return response()->json([
                'success' => true,
                'data' => $status,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's transaction history.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getTransactionHistory(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $perPage = $request->get('per_page', 20);
            $type = $request->get('type'); // filter by type
            $source = $request->get('source'); // filter by source

            $query = $user->transactions()->latest();

            if ($type) {
                $query->where('type', $type);
            }

            if ($source) {
                $query->where('source', $source);
            }

            $transactions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $transactions,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's TNT balance.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getBalance(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'balance_tnt' => (float) $user->balance_tnt,
                    'user_id' => $user->id,
                    'username' => $user->username,
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

