<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\{Tenant, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(string $tenantId): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Check if user is admin
        if ($user->isAdmin()) {
            return $this->adminDashboard($tenant, $user);
        }

        // Regular user dashboard
        return $this->userDashboard($tenant, $user);
    }

    /**
     * Admin Dashboard with full statistics
     */
    private function adminDashboard(Tenant $tenant, User $user): View
    {
        $stats = [
            'total_users' => User::where('tenant_id', $tenant->id)->count(),
            'developers' => User::where('tenant_id', $tenant->id)->where('role', 'developer')->count(),
            'work_bees' => User::where('tenant_id', $tenant->id)->where('role', 'work-bee')->count(),
            'standard_users' => User::where('tenant_id', $tenant->id)->where('role', 'standard')->count(),
            'admins' => User::where('tenant_id', $tenant->id)->where('role', 'admin')->count(),
        ];

        $recentUsers = User::where('tenant_id', $tenant->id)
            ->latest()
            ->take(10)
            ->get();

        return view('tenant.dashboard.admin', compact(
            'tenant',
            'user',
            'stats',
            'recentUsers'
        ));
    }

    /**
     * Regular User Dashboard
     */
    private function userDashboard(Tenant $tenant, User $user): View
    {
        $stats = [
            'total_team' => User::where('tenant_id', $tenant->id)->count(),
            'developers' => User::where('tenant_id', $tenant->id)->where('role', 'developer')->count(),
            'work_bees' => User::where('tenant_id', $tenant->id)->where('role', 'work-bee')->count(),
            'standard_users' => User::where('tenant_id', $tenant->id)->where('role', 'standard')->count(),
        ];

        $teamMembers = User::where('tenant_id', $tenant->id)
            ->where('id', '!=', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('tenant.dashboard.user', compact(
            'tenant',
            'user',
            'stats',
            'teamMembers'
        ));
    }
}