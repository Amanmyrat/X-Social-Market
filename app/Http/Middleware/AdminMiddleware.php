<?php

namespace App\Http\Middleware;

use App\Enum\ErrorMessage;
use App\Models\Admin;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Admin $user */
        $user = Auth::user();

        if ($user->tokenCan('role:admin')) {
            if (! $user->is_active) {
                return response()->json(
                    [
                        'message' => ErrorMessage::ACCOUNT_DISABLED_ERROR->value,
                    ], 403);
            }
            $user->update(['last_activity' => now()]);

            return $next($request);
        }

        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
}
