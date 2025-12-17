<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        try {
            // Check database connection
            \DB::connection()->getPdo();
            
            // Get stats
            $stats = [
                'total_tenants' => Tenant::count(),
                'active_tenants' => Tenant::where('status', 'active')->count(),
                'expired_tenants' => Tenant::where('expires_at', '<=', now())->count(),
            ];
            
            return response()->json([
                'status' => 'ok',
                'tool' => config('platform.tool.name'),
                'domain' => config('platform.tool.domain'),
                'timestamp' => now()->toIso8601String(),
                'database' => 'connected',
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}