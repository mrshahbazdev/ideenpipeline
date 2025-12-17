<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            // Get tenant from request attributes (set by IdentifyTenant middleware)
            $tenant = $request->attributes->get('tenant');
            
            if ($tenant) {
                \Log::info('Auth redirect: Using tenant from attributes', [
                    'tenant_id' => $tenant->id,
                    'subdomain' => $tenant->subdomain,
                ]);
                return route('tenant.login', ['tenantId' => $tenant->id]);
            }

            // Try to get tenantId from route parameter
            $tenantId = $request->route('tenantId');
            if ($tenantId) {
                \Log::info('Auth redirect: Using tenantId from route', [
                    'tenantId' => $tenantId,
                ]);
                return route('tenant.login', ['tenantId' => $tenantId]);
            }

            \Log::warning('Auth redirect: No tenant found, redirecting to home');
            return route('home');
        }

        return null;
    }
}