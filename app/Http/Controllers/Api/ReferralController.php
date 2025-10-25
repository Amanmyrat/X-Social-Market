<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ReferralController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Get user's referral information.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getReferralInfo(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $info = $this->referralService->getReferralInfo($user);

            return response()->json([
                'success' => true,
                'data' => $info,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate a referral code.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateReferralCode(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'referral_code' => 'required|string|size:8',
            ]);

            $isValid = $this->referralService->validateReferralCode($request->referral_code);

            return response()->json([
                'success' => true,
                'data' => [
                    'valid' => $isValid,
                    'referral_code' => $request->referral_code,
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

