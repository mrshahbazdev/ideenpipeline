<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\{Tenant, User, Idea};
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
            'total_ideas' => Idea::where('tenant_id', $tenant->id)->count(),
            'pending_review' => Idea::where('tenant_id', $tenant->id)->where('status', 'new')->count(),
            'pending_pricing' => Idea::where('tenant_id', $tenant->id)->where('status', 'pending_pricing')->count(),
            'approved_ideas' => Idea::where('tenant_id', $tenant->id)->where('status', 'approved')->count(),
            'total_budget' => Idea::where('tenant_id', $tenant->id)
                ->where('status', 'approved')
                ->sum('cost'),
            'developers' => User::where('tenant_id', $tenant->id)->where('role', 'developer')->count(),
            'work_bees' => User::where('tenant_id', $tenant->id)->where('role', 'work-bee')->count(),
        ];

        $recentUsers = User::where('tenant_id', $tenant->id)
            ->latest()
            ->take(5)
            ->get();

        $recentIdeas = Idea::where('tenant_id', $tenant->id)
            ->with('creator')
            ->latest()
            ->take(5)
            ->get();

        return view('tenant.dashboard.admin', compact(
            'tenant',
            'user',
            'stats',
            'recentUsers',
            'recentIdeas'
        ));
    }

    /**
     * Regular User Dashboard
     */
    private function userDashboard(Tenant $tenant, User $user): View
    {
        $stats = [
            'my_ideas' => Idea::where('tenant_id', $tenant->id)
                ->where('created_by', $user->id)
                ->count(),
            'approved_ideas' => Idea::where('tenant_id', $tenant->id)
                ->where('created_by', $user->id)
                ->where('status', 'approved')
                ->count(),
            'pending_ideas' => Idea::where('tenant_id', $tenant->id)
                ->where('created_by', $user->id)
                ->whereIn('status', ['new', 'pending_pricing'])
                ->count(),
            'total_team' => User::where('tenant_id', $tenant->id)->count(),
        ];

        $myIdeas = Idea::where('tenant_id', $tenant->id)
            ->where('created_by', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $allIdeas = Idea::where('tenant_id', $tenant->id)
            ->with('creator')
            ->latest()
            ->take(5)
            ->get();

        return view('tenant.dashboard.user', compact(
            'tenant',
            'user',
            'stats',
            'myIdeas',
            'allIdeas'
        ));
    }
}