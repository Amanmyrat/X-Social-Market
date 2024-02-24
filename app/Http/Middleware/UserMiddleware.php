<?php

namespace App\Http\Middleware;

use App\Enum\ErrorMessage;
use App\Models\User;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->tokenCan('role:user')) {
            if ($user->blocked_at) {
                return response()->json(
                    [
                        'message' => ErrorMessage::ACCOUNT_BLOCKED_ERROR->value,
                        'reason' => $user->block_reason,
                    ], 403);
            }

            return $next($request);
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }
}
