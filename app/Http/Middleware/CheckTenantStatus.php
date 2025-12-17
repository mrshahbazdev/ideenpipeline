<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        // If no tenant found (shouldn't happen with tenancy middleware)
        if (!$tenant) {
            return redirect()->to(config('platform.url'))
                ->with('error', 'Tenant not found');
        }

        // Check if tenant is inactive/suspended
        if ($tenant->status !== 'active') {
            return redirect()->route('tenant.suspended');
        }

        // Check if tenant subscription is expired
        if ($tenant->isExpired()) {
            return redirect()->route('tenant.upgrade');
        }

        return $next($request);
    }
}