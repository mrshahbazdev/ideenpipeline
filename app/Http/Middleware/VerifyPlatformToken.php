<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPlatformToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $platformToken = config('platform.api_token');

        if (!$token || !$platformToken || $token !== $platformToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - Invalid platform token'
            ], 401);
        }

        return $next($request);
    }
}