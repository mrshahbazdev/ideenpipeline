<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next)
    {
        // Get tenant from route parameter
        $tenantId = $request->route('tenantId');
        
        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            
            if (!$tenant || !$tenant->isActive()) {
                abort(403, 'Tenant not found or inactive');
            }
            
            // Set tenant in session
            session(['tenant_id' => $tenantId]);
            
            // Share with views
            view()->share('tenant', $tenant);
        }

        return $next($request);
    }
}