<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Exception;

class AuthService
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Register user with OTP.
     * OTP validation happens in RegisterRequest via custom rule.
     *
     * @throws Exception
     */
    public function register(array $registerData): ?User
    {
        return $this->otpService->register($registerData);
    }

    /**
     * Login user with OTP.
     * OTP validation happens in LoginRequest via custom rule.
     *
     * @throws Exception
     */
    public function login(array $loginData): User
    {
        return $this->otpService->login($loginData);
    }
}
