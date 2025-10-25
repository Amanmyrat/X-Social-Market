<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Exception;
use Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * @throws Exception
     */
    public function register($registerData): ?User
    {
        $user = User::create([
            'username' => 'ulanyjy_'.random_int(10000000, 99999999),
            'phone' => $registerData['phone'],
            'password' => Hash::make($registerData['password']),
            'device_token' => $registerData['device_token'],
            'last_activity' => now(),
            'type' => User::TYPE_USER,
        ]);

        // Generate unique referral code for the new user
        if ($user) {
            $user->referral_code = User::generateReferralCode();
            $user->save();
            
            $user->profile()->create([]);
            
            // Process referral if code was provided
            if (!empty($registerData['referral_code'])) {
                try {
                    $result = $this->referralService->processReferral($user, $registerData['referral_code']);
                    Log::info('Referral processed successfully', [
                        'new_user_id' => $user->id,
                        'referrer_id' => $result['referrer']->id,
                        'reward' => $result['reward_amount']
                    ]);
                } catch (Exception $e) {
                    // Log error but don't block registration
                    Log::warning('Referral processing failed', [
                        'user_id' => $user->id,
                        'referral_code' => $registerData['referral_code'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $user;
    }

    /**
     * @throws ValidationException
     */
    public function login(LoginRequest $request): void
    {
        $request->authenticate();
        $request->user()->update(
            [
                'device_token' => $request->device_token,
                'last_activity' => now(),
            ]
        );
    }
}
