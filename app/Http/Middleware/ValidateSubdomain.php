<?php

namespace App\Http\Middleware;

use App\Models\Tenant;  // CRM tool mein Tenant model hai
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

        // If no subdomain or accessing by IP, skip validation
        if (!$subdomain || $this->isIpAddress($host)) {
            return $next($request);
        }

        // Check if subdomain exists in tenants
        $tenant = Tenant::where('subdomain', $subdomain)
            ->where('status', 'active')
            ->first();

        if (!$tenant) {
            // Invalid subdomain - show 404
            return response()->view('errors.subdomain-not-found', [
                'subdomain' => $subdomain,
                'host' => $host,
            ], 404);
        }

        if (!$tenant->isActive()) {
            return response()->view('errors.tenant-expired', [
                'tenant' => $tenant,
            ], 403);
        }

        // Valid subdomain - attach to request
        $request->attributes->set('tenant', $tenant);
        $request->attributes->set('tenant_id', $tenant->id);

        return $next($request);
    }

    /**
     * Extract subdomain from host
     */
    private function extractSubdomain(string $host): ?string
    {
        // Remove port if present
        $host = explode(':', $host)[0];
        
        // For local development (localhost, 127.0.0.1)
        if ($this->isLocalHost($host)) {
            return null;
        }
        
        // Get base domain from config
        $baseDomain = config('app.base_domain', 'ideenpipeline.de');
        
        // Remove base domain
        $subdomain = str_replace('.' . $baseDomain, '', $host);
        
        // If subdomain is same as host, no subdomain exists
        if ($subdomain === $host) {
            return null;
        }
        
        // Get first part only (in case of multi-level subdomains)
        $parts = explode('.', $subdomain);
        
        return $parts[0] ?? null;
    }

    /**
     * Check if host is localhost
     */
    private function isLocalHost(string $host): bool
    {
        return in_array($host, [
            'localhost',
            '127.0.0.1',
            '::1',
        ]);
    }

    /**
     * Check if host is IP address
     */
    private function isIpAddress(string $host): bool
    {
        $host = explode(':', $host)[0]; // Remove port
        return filter_var($host, FILTER_VALIDATE_IP) !== false;
    }
}