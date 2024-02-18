<?php

namespace App\Services;

use App\Models\OtpCode;
use Carbon\Carbon;
use Exception;
use Http;
use Illuminate\Http\Request;

class OtpService
{
    /**
     * @throws Exception
     */
    public static function sendOTP(Request $request): int
    {
        $validated = $request->validate(
            [
                'phone' => ['required', 'integer'],
            ]
        );

        $code = random_int(1000, 9999);
        $response = Http::post(config('otp.url'), [
            'phoneNumber' => '+993'.$validated['phone'],
            'code' => 'Siziň gysga wagtlaýyn tassyklaak üçin koduňyz: '.$code,
        ]);

        if ($response->status() == 200) {
            OtpCode::create(['phone' => $validated['phone'], 'code' => $code, 'valid_until' => Carbon::now()->addMinutes(config('otp.timeout'))]);

            return $code;
        }

        return -1;
    }

    public static function confirmOTP(Request $request): int
    {
        $validated = $request->validate(
            [
                'code' => ['required', 'integer', 'between:1000,9999'],
                'phone' => ['required', 'integer'],
            ]
        );

        $otpCode = OtpCode::where('phone', $validated['phone'])->first();

        if (! isset($otpCode)) {
            return -1;
        }
        if ($otpCode->code != $validated['code']) {
            return -1;
        }
        if (Carbon::now() > $otpCode->valid_until) {
            return 0;
        }

        return 1;
    }
}
