<?php

namespace App\Services;

use App\Enum\ErrorMessage;
use App\Models\OtpCode;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Hash;
use Illuminate\Support\Facades\Log;

class OtpService
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Send OTP code to phone number.
     *
     * @throws Exception
     */
    public function sendOtp(array $validated): int
    {
        $code = random_int(1000, 9999);
        $phoneNumber = '+993'.$validated['phone'];
        $message = "Sizin Tanat kodunyz: $code";

        $command = 'gammu sendsms TEXT '.escapeshellarg($phoneNumber).' -text '.escapeshellarg($message);

        // Execute the command
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            // Delete any old OTP codes for this phone
            OtpCode::where('phone', $validated['phone'])->delete();

            // Create new OTP
            OtpCode::create([
                'phone' => $validated['phone'],
                'code' => $code,
                'valid_until' => Carbon::now()->addMinutes(config('otp.timeout', 10)),
            ]);

            return $code;
        }

        throw new Exception(ErrorMessage::OTP_DID_NOT_SENT_ERROR->value);
    }

    /**
     * Verify OTP code.
     *
     * @throws Exception
     */
    public function confirmOtp(array $validated): void
    {
        $otpCode = OtpCode::where('phone', $validated['phone'])->latest()->first();

        if (!$otpCode || $otpCode->code != $validated['code']) {
            throw new Exception(ErrorMessage::OTP_DID_NOT_MATCH_ERROR->value);
        }

        if (Carbon::now() > $otpCode->valid_until) {
            throw new Exception(ErrorMessage::OTP_TIMEOUT_ERROR->value);
        }

        // Delete OTP after successful verification
        $otpCode->delete();
    }

    /**
     * Register user with OTP verification (done in request validation).
     * Request validation already verified the OTP.
     *
     * @throws Exception
     */
    public function register(array $validated): User
    {
        // OTP already validated in RegisterRequest
        // Create user
        $user = User::create([
            'username' => 'ulanyjy_'.random_int(10000000, 99999999),
            'phone' => $validated['phone'],
            'password' => null, // OTP-only, no password
            'device_token' => $validated['device_token'],
            'last_activity' => now(),
            'phone_verified_at' => now(),
            'type' => User::TYPE_USER,
        ]);

        // Generate referral code
        if ($user) {
            $user->referral_code = User::generateReferralCode();
            $user->save();

            $user->profile()->create([]);

            // Process referral if code was provided
            if (!empty($validated['referral_code'])) {
                try {
                    $result = $this->referralService->processReferral($user, $validated['referral_code']);
                    Log::info('Referral processed successfully', [
                        'new_user_id' => $user->id,
                        'referrer_id' => $result['referrer']->id,
                        'reward' => $result['reward_amount']
                    ]);
                } catch (Exception $e) {
                    Log::warning('Referral processing failed', [
                        'user_id' => $user->id,
                        'referral_code' => $validated['referral_code'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $user;
    }

    /**
     * Login user with OTP verification (done in request validation).
     * Request validation will verify the OTP.
     *
     * @throws Exception
     */
    public function login(array $validated): User
    {
        // OTP code already validated in LoginRequest via custom rule
        $field = filter_var($validated['login'], FILTER_VALIDATE_INT) ? 'phone' : 'username';

        $user = User::where($field, $validated['login'])->first();

        if (!$user) {
            throw new Exception('Ulanyjy tapylmady.');
        }

        if ($user->blocked_at) {
            throw new Exception('Sizin hasaba gapady gosuldy. Sebäbi: '.$user->block_reason);
        }

        // Update user's device token and last activity
        $user->update([
            'device_token' => $validated['device_token'] ?? $user->device_token,
            'last_activity' => now(),
            'phone_verified_at' => $user->phone_verified_at ?? now(),
        ]);

        return $user;
    }
}
