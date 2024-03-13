<?php

namespace App\Services;

use App\Enum\ErrorMessage;
use App\Models\OtpCode;
use Carbon\Carbon;
use Exception;

class OtpService
{
    /**
     * @throws Exception
     */
    public function sendOTP(array $validated): int
    {
        $code = random_int(1000, 9999);
        $phoneNumber = '+993'.$validated['phone'];
        $message = "Sizin Tanat kodunyz: $code";

        $command = 'gammu sendsms TEXT ' . escapeshellarg($phoneNumber) . ' -text ' . escapeshellarg($message);

        // Execute the command
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            OtpCode::create([
                'phone' => $validated['phone'],
                'code' => $code,
                'valid_until' => Carbon::now()->addMinutes(config('otp.timeout')),
            ]);

            return $code;
        }

        throw new Exception(ErrorMessage::OTP_DID_NOT_SENT_ERROR->value);
    }

    /**
     * @throws Exception
     */
    public static function confirmOTP(array $validated): void
    {
        $otpCode = OtpCode::where('phone', $validated['phone'])->first();

        if (! $otpCode || $otpCode->code != $validated['code']) {
            throw new Exception(ErrorMessage::OTP_DID_NOT_MATCH_ERROR->value);
        }

        if (Carbon::now() > $otpCode->valid_until) {
            throw new Exception(ErrorMessage::OTP_TIMEOUT_ERROR->value);
        }

    }
}
