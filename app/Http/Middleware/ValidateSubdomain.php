<?php

namespace App\Http\Middleware;

use App\Models\Subscription; // ← Add this line
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateSubdomain
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get subdomain from host
        $host = $request->getHost();
        $subdomain = $this->extractSubdomain($host);

        // If no subdomain or is main domain, continue
        if (!$subdomain || $subdomain === 'www') {
            return $next($request);
        }

        // Check if subdomain exists in subscriptions
        $subscription = Subscription::where('subdomain', $subdomain)
            ->where('status', 'active')
            ->where('is_tenant_active', true)
            ->first();

        if (!$subscription) {
            // Invalid subdomain - show 404
            abort(404, 'Subdomain not found. This tenant does not exist or has been deactivated.');
        }

        // Valid subdomain - attach to request for later use
        $request->attributes->set('subscription', $subscription);
        $request->attributes->set('tenant_id', $subscription->tenant_id);

        return $next($request);
    }

    /**
     * Extract subdomain from host
     */
    private function extractSubdomain(string $host): ?string
    {
        // Get base domain from config or env
        $baseDomain = config('app.base_domain', 'ideenpipeline.de');
        
        // Remove base domain
        $subdomain = str_replace('.' . $baseDomain, '', $host);
        
        // If subdomain is same as host, no subdomain exists
        if ($subdomain === $host) {
            return null;
        }
        
        // If subdomain has multiple parts (e.g., testing.subdomain), get first part
        $parts = explode('.', $subdomain);
        
        return $parts[0] ?? null;
    }
}