<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('sanctum')->user()->tokenCan('role:user')) {
            if(auth('sanctum')->user()->blocked_at){
                return response()->json(['message' => 'Your account is blocked. Reason: ' . auth('sanctum')->user()->block_reason], 403);
            }
            return $next($request);
        }
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
}
