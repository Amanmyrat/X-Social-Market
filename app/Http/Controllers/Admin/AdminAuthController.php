<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    /**
     * Admin login
     *
     * @unauthenticated
     */
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $admin = Admin::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'Authorization failed',
                'success' => false,
            ], 401);
        }

        return response()->json([
            'admin' => $admin,
            'token' => $admin->createToken('mobile', ['role:admin'])->plainTextToken,
            'success' => true,
        ]);

    }
}
