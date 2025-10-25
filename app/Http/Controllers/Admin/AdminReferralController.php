<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AdminReferralController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Update referral reward setting.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateReferralReward(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'referral_reward' => 'required|numeric|min:0|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $referralReward = $request->input('referral_reward');

            AppSetting::set(
                'referral_reward',
                $referralReward,
                'decimal',
                'Reward amount for successful referral (TNT coins)'
            );

            return response()->json([
                'success' => true,
                'message' => 'Referral reward updated successfully',
                'data' => [
                    'referral_reward' => (float) $referralReward,
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get referral reward setting.
     *
     * @return JsonResponse
     */
    public function getReferralReward(): JsonResponse
    {
        try {
            $referralReward = AppSetting::get('referral_reward', 10);

            return response()->json([
                'success' => true,
                'data' => [
                    'referral_reward' => (float) $referralReward,
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get referral statistics for admin dashboard.
     *
     * @return JsonResponse
     */
    public function getReferralStatistics(): JsonResponse
    {
        try {
            $statistics = $this->referralService->getStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

