<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr; // ✅ Add this for Laravel 11
use Carbon\Carbon;

class TenantController extends Controller
{
    /**
     * Create new tenant (Single Database Multi-Tenancy)
     */
    public function create(Request $request): JsonResponse
    {
        // Validate API token
        if (!$this->validateApiToken($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API token.',
            ], 401);
        }

        $validated = $request->validate([
            'tenant_id' => 'required|string|unique:tenants,id',
            'subdomain' => 'required|string|unique:tenants,subdomain|alpha_dash',
            'subscription_id' => 'required',
            'user_id' => 'required',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => 'nullable|string|min:8',
            'admin_password_hash' => 'nullable|string', // Accept hashed password
            'package_name' => 'required|string|max:255',
            'starts_at' => 'required|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        DB::beginTransaction();

        try {
            $startsAt = Carbon::parse($validated['starts_at']);
            $expiresAt = isset($validated['expires_at']) 
                ? Carbon::parse($validated['expires_at'])
                : null;

            $baseDomain = config('app.base_domain', 'ideenpipeline.de');
            $domain = $validated['subdomain'] . '.' . $baseDomain;

            // Create tenant
            $tenant = Tenant::create([
                'id' => $validated['tenant_id'],
                'platform_subscription_id' => $validated['subscription_id'],
                'platform_user_id' => $validated['user_id'],
                'admin_name' => $validated['admin_name'],
                'admin_email' => $validated['admin_email'],
                'package_name' => $validated['package_name'],
                'subdomain' => $validated['subdomain'],
                'domain' => $domain,
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'status' => 'active',
            ]);

            // Determine password
            if (!empty($validated['admin_password_hash'])) {
                // Use pre-hashed password
                $passwordToStore = $validated['admin_password_hash'];
            } elseif (!empty($validated['admin_password'])) {
                // Hash plain password
                $passwordToStore = Hash::make($validated['admin_password']);
            } else {
                throw new \Exception('Either admin_password or admin_password_hash required');
            }

            // Create user with hashed password
            $user = User::withoutGlobalScope('tenant')->create([
                'tenant_id' => $tenant->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => $passwordToStore, // Store hash directly
                'email_verified_at' => now(),
                'role' => 'admin',
            ]);

            DB::commit();

            Log::info('Tenant created successfully', [
                'tenant_id' => $tenant->id,
                'subdomain' => $tenant->subdomain,
                'admin_email' => $tenant->admin_email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully',
                'data' => [
                    'tenant_id' => $tenant->id,
                    'subdomain' => $tenant->subdomain,
                    'domain' => $tenant->domain,
                    'admin_user_id' => $user->id,
                    'admin_email' => $user->email,
                    'starts_at' => $tenant->starts_at->toIso8601String(),
                    'expires_at' => $tenant->expires_at?->toIso8601String(),
                    'is_active' => $tenant->isActive(),
                    'status' => $tenant->status,
                    'login_url' => "https://{$tenant->domain}/tenant/{$tenant->id}/login",
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Tenant creation failed', [
                'error' => $e->getMessage(),
                'data' => Arr::except($validated ?? [], ['admin_password', 'admin_password_hash']), // ✅ Fixed
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Tenant creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update tenant password (sync from main platform)
     */
    public function updatePassword(Request $request, string $tenantId): JsonResponse
    {
        // Validate API token
        if (!$this->validateApiToken($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API token.',
            ], 401);
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        try {
            // Find tenant
            $tenant = Tenant::find($tenantId);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found',
                ], 404);
            }

            // Find user in this tenant
            $user = User::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenantId)
                ->where('email', $validated['email'])
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found in this tenant',
                ], 404);
            }

            // Update password
            $user->password = Hash::make($validated['password']);
            $user->save();

            Log::info('Password updated for tenant user', [
                'tenant_id' => $tenantId,
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully',
                'data' => [
                    'tenant_id' => $tenantId,
                    'user_id' => $user->id,
                    'email' => $user->email,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Password update failed', [
                'tenant_id' => $tenantId,
                'email' => $validated['email'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Password update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tenant status
     */
    public function status(string $tenantId): JsonResponse
    {
        try {
            $tenant = Tenant::find($tenantId);

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found',
                ], 404);
            }

            // Get users count
            $usersCount = User::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenantId)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'tenant_id' => $tenant->id,
                    'subdomain' => $tenant->subdomain,
                    'domain' => $tenant->domain,
                    'status' => $tenant->status,
                    'is_active' => $tenant->isActive(),
                    'is_expired' => $tenant->isExpired(),
                    'days_remaining' => $tenant->daysRemaining(),
                    'starts_at' => $tenant->starts_at?->toIso8601String(),
                    'expires_at' => $tenant->expires_at?->toIso8601String(),
                    'package_name' => $tenant->package_name,
                    'admin_name' => $tenant->admin_name,
                    'admin_email' => $tenant->admin_email,
                    'users_count' => $usersCount,
                    'created_at' => $tenant->created_at->toIso8601String(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get tenant status', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get tenant status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update tenant status (activate/deactivate)
     */
    public function updateStatus(Request $request, string $tenantId): JsonResponse
    {
        // Validate API token
        if (!$this->validateApiToken($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API token.',
            ], 401);
        }

        $validated = $request->validate([
            'status' => 'required|in:active,inactive,suspended',
            'expires_at' => 'nullable|date',
        ]);

        try {
            $tenant = Tenant::findOrFail($tenantId);

            $oldStatus = $tenant->status;
            
            $tenant->status = $validated['status'];
            
            if (isset($validated['expires_at'])) {
                $tenant->expires_at = Carbon::parse($validated['expires_at']);
            }

            $tenant->save();

            Log::info('Tenant status updated', [
                'tenant_id' => $tenantId,
                'old_status' => $oldStatus,
                'new_status' => $tenant->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant status updated successfully',
                'data' => [
                    'tenant_id' => $tenant->id,
                    'status' => $tenant->status,
                    'is_active' => $tenant->isActive(),
                    'expires_at' => $tenant->expires_at?->toIso8601String(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update tenant status', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update tenant status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete tenant (soft delete - set status to inactive)
     */
    public function delete(Request $request, string $tenantId): JsonResponse
    {
        // Validate API token
        if (!$this->validateApiToken($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API token.',
            ], 401);
        }

        try {
            $tenant = Tenant::findOrFail($tenantId);

            // Soft delete - just set status to inactive
            $tenant->status = 'inactive';
            $tenant->save();

            Log::warning('Tenant deactivated', [
                'tenant_id' => $tenantId,
                'subdomain' => $tenant->subdomain,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant deactivated successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to deactivate tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate tenant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Health check endpoint
     */
    public function health(): JsonResponse
    {
        try {
            $stats = [
                'total_tenants' => Tenant::count(),
                'active_tenants' => Tenant::where('status', 'active')->count(),
                'expired_tenants' => Tenant::where('expires_at', '<', now())->count(),
                'total_users' => User::withoutGlobalScope('tenant')->count(),
            ];

            return response()->json([
                'status' => 'ok',
                'tool' => 'CRM Tool - Innovation Pipeline',
                'domain' => config('app.url'),
                'timestamp' => now()->toIso8601String(),
                'database' => 'connected',
                'architecture' => 'Single Database Multi-Tenancy',
                'laravel_version' => app()->version(),
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate API token from request
     */
    private function validateApiToken(Request $request): bool
    {
        $token = $request->bearerToken() ?? $request->header('Authorization');
        
        // Remove 'Bearer ' prefix if present
        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }
        
        $expectedToken = config('app.platform_api_token', 'test-token-12345');
        
        return hash_equals($expectedToken, $token ?? ''); // ✅ Use hash_equals for security
    }
}