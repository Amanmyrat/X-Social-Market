<?php

namespace App\Http\Controllers\Api;

use App\Services\OtpService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OtpController extends ApiBaseController
{

    /**
     * Send otp code to the given phone
     *
     * @unauthenticated
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function sendOTP(Request $request): JsonResponse
    {
        $code = OtpService::sendOtp($request);

        if ($code != -1){
            return $this->respondWithArray([
                'success' => true,
                'data' => [
                    'code' => $code,
                ]
            ]);
        }else{
            return $this->respondWithError('Error occurred', 400);
        }

    }

    /**
     * Confirm otp code to the given phone
     *
     * @unauthenticated
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function confirmOTP(Request $request): JsonResponse
    {
        $confirmed = OtpService::confirmOtp($request);

        if ($confirmed == -1) {
            $this->setStatusCode(400);
            return $this->respondWithError('OTP did not match', 400);
        }elseif ($confirmed == 0){
            $this->setStatusCode(400);
            return $this->respondWithError('OTP timeout', 400);
        }

        return $this->respondWithArray([
            'success' => true,
        ]);

    }

}
