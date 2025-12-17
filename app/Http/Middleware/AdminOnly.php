<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(401, 'Unauthenticated.');
        }

        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can access this area.');
        }

        return $next($request);
    }
}