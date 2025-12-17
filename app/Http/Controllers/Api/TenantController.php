<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    /**
     * Create new tenant (no separate database)
     */
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_id' => 'required|string|unique:tenants,id',
            'subdomain' => 'required|string|unique:tenants,subdomain',
            'subscription_id' => 'required',
            'user_id' => 'required',
            'admin_name' => 'required|string',
            'admin_email' => 'required|email',
            'admin_password' => 'required|string|min:8',
            'package_name' => 'required|string',
            'starts_at' => 'required|date',
            'expires_at' => 'nullable|date|after:starts_at',
        ]);

        DB::beginTransaction();

        try {
            $startsAt = \Carbon\Carbon::parse($validated['starts_at']);
            $expiresAt = isset($validated['expires_at']) 
                ? \Carbon\Carbon::parse($validated['expires_at'])
                : null;

            // Generate proper domain based on environment
            $baseDomain = config('app.base_domain', 'ideenpipeline.de');
            $domain = $validated['subdomain'] . '.' . $baseDomain;

            // Create tenant record
            $tenant = Tenant::create([
                'id' => $validated['tenant_id'],
                'platform_subscription_id' => $validated['subscription_id'],
                'platform_user_id' => $validated['user_id'],
                'admin_name' => $validated['admin_name'],
                'admin_email' => $validated['admin_email'],
                'package_name' => $validated['package_name'],
                'subdomain' => $validated['subdomain'],
                'domain' => $domain, // ← Fixed: Use production domain
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'status' => 'active',
            ]);

            // Create admin user
            $user = User::withoutGlobalScope('tenant')->create([
                'tenant_id' => $tenant->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]);

            DB::commit();

            \Log::info('Tenant created successfully', [
                'tenant_id' => $tenant->id,
                'subdomain' => $tenant->subdomain,
                'domain' => $tenant->domain,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully',
                'data' => [
                    'tenant_id' => $tenant->id,
                    'subdomain' => $tenant->subdomain,
                    'domain' => $tenant->domain,
                    'admin_user_id' => $user->id,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Tenant creation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Tenant creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update tenant password
     */
    public function updatePassword(Request $request, string $tenantId): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        try {
            $tenant = Tenant::findOrFail($tenantId);

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

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Health check
     */
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'tool' => 'CRM Tool',
            'database' => 'Single Database Multi-Tenancy',
            'tenants_count' => Tenant::count(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}