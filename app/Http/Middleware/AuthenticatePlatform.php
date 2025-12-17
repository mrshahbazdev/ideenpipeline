<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePlatform
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->header('Authorization');
        
        // Remove 'Bearer ' prefix if present
        $token = str_replace('Bearer ', '', $token);
        
        // Get expected token from config
        $expectedToken = config('app.platform_api_token');
        
        if (!$token || $token !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API token.',
            ], 401);
        }
        
        return $next($request);
    }
}