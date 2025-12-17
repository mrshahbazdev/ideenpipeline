<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TenantController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Single Database Multi-Tenancy API
| All tenant management endpoints for CRM tool
|
*/

// Public Health Check - No authentication required
Route::get('/health', [TenantController::class, 'health'])
    ->name('api.health');

// Tenant Management Endpoints - Require API token validation (handled in controller)
Route::prefix('tenants')->name('api.tenants.')->group(function () {
    
    // Create new tenant
    Route::post('/create', [TenantController::class, 'create'])
        ->name('create');
    
    // Get tenant status
    Route::get('/{tenantId}/status', [TenantController::class, 'status'])
        ->name('status');
    
    // Update tenant password
    Route::post('/{tenantId}/update-password', [TenantController::class, 'updatePassword'])
        ->name('update-password');
    
    // Update tenant status (activate/deactivate/suspend)
    Route::post('/{tenantId}/update-status', [TenantController::class, 'updateStatus'])
        ->name('update-status');
    
    // Deactivate tenant (soft delete)
    Route::delete('/{tenantId}', [TenantController::class, 'delete'])
        ->name('delete');
});

// Additional utility endpoints
Route::prefix('stats')->name('api.stats.')->group(function () {
    
    // Get overall statistics
    Route::get('/overview', function () {
        try {
            $stats = [
                'total_tenants' => \App\Models\Tenant::count(),
                'active_tenants' => \App\Models\Tenant::where('status', 'active')->count(),
                'inactive_tenants' => \App\Models\Tenant::where('status', 'inactive')->count(),
                'suspended_tenants' => \App\Models\Tenant::where('status', 'suspended')->count(),
                'expired_tenants' => \App\Models\Tenant::where('expires_at', '<', now())
                    ->where('status', 'active')
                    ->count(),
                'expiring_soon' => \App\Models\Tenant::where('expires_at', '>', now())
                    ->where('expires_at', '<=', now()->addDays(7))
                    ->where('status', 'active')
                    ->count(),
                'total_users' => \App\Models\User::withoutGlobalScope('tenant')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toIso8601String(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    })->name('overview');
    
    // Get tenants by status
    Route::get('/by-status/{status}', function (string $status) {
        try {
            if (!in_array($status, ['active', 'inactive', 'suspended'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status. Must be: active, inactive, or suspended',
                ], 400);
            }

            $tenants = \App\Models\Tenant::where('status', $status)
                ->select('id', 'subdomain', 'domain', 'admin_name', 'package_name', 'starts_at', 'expires_at', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($tenant) {
                    return [
                        'tenant_id' => $tenant->id,
                        'subdomain' => $tenant->subdomain,
                        'domain' => $tenant->domain,
                        'admin_name' => $tenant->admin_name,
                        'package_name' => $tenant->package_name,
                        'is_active' => $tenant->isActive(),
                        'is_expired' => $tenant->isExpired(),
                        'days_remaining' => $tenant->daysRemaining(),
                        'starts_at' => $tenant->starts_at?->toIso8601String(),
                        'expires_at' => $tenant->expires_at?->toIso8601String(),
                        'created_at' => $tenant->created_at->toIso8601String(),
                    ];
                });

            return response()->json([
                'success' => true,
                'status' => $status,
                'count' => $tenants->count(),
                'data' => $tenants,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tenants',
                'error' => $e->getMessage(),
            ], 500);
        }
    })->name('by-status');
});

// Debug endpoint (only in non-production)
if (!app()->environment('production')) {
    Route::get('/debug/info', function () {
        return response()->json([
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
            'app_url' => config('app.url'),
            'base_domain' => config('app.base_domain'),
            'database' => [
                'driver' => config('database.default'),
                'connected' => true,
            ],
            'routes_count' => count(Route::getRoutes()),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ]);
    })->name('api.debug.info');
}