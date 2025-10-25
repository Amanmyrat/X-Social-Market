<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Services\DailyLoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AdminSettingsController extends Controller
{
    protected DailyLoginService $dailyLoginService;

    public function __construct(DailyLoginService $dailyLoginService)
    {
        $this->dailyLoginService = $dailyLoginService;
    }

    /**
     * Update daily login base reward setting.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateDailyLoginReward(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'base_reward' => 'required|numeric|min:0|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $baseReward = $request->input('base_reward');

            AppSetting::set(
                'daily_login_base_reward',
                $baseReward,
                'decimal',
                'Base reward for daily login (TNT coins)'
            );

            return response()->json([
                'success' => true,
                'message' => 'Daily login base reward updated successfully',
                'data' => [
                    'base_reward' => (float) $baseReward,
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
     * Get daily login reward settings.
     *
     * @return JsonResponse
     */
    public function getDailyLoginReward(): JsonResponse
    {
        try {
            $baseReward = AppSetting::get('daily_login_base_reward', 2);

            return response()->json([
                'success' => true,
                'data' => [
                    'base_reward' => (float) $baseReward,
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
     * Get daily login statistics for admin dashboard.
     *
     * @return JsonResponse
     */
    public function getDailyLoginStatistics(): JsonResponse
    {
        try {
            $statistics = $this->dailyLoginService->getStatistics();

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

