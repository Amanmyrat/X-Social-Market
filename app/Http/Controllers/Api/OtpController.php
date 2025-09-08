<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Otp\OtpConfirmRequest;
use App\Http\Requests\Otp\OtpSendRequest;
use App\Services\OtpService;
use Exception;
use Illuminate\Http\JsonResponse;

class OtpController
{
    public function __construct(protected OtpService $service)
    {
    }

    /**
     * Send otp code
     *
     * @unauthenticated
     *
     * @throws Exception
     */
    public function sendOTP(OtpSendRequest $request): JsonResponse
    {
        try {
            $code = $this->service->sendOtp($request->validated());

            return response()->json([
                'success' => true,
                'data' => ['code' => $code],
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Confirm otp code
     *
     * @unauthenticated
     */
    public function confirmOTP(OtpConfirmRequest $request): JsonResponse
    {
        try {
            $this->service->confirmOtp($request->validated());

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }

    }
}
