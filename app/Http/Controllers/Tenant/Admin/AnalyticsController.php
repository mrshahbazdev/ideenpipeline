<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Tenant, User, Team, Idea, IdeaVote, IdeaComment};
use Illuminate\Support\Facades\{DB, Auth};
use Illuminate\View\View;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(string $tenantId): View
    {
        $tenant = Tenant::findOrFail($tenantId);

        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        // User Analytics
        $userStats = [
            'total' => User::where('tenant_id', $tenant->id)->count(),
            'active' => User::where('tenant_id', $tenant->id)->where('is_active', true)->count(),
            'new_this_month' => User::where('tenant_id', $tenant->id)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'by_role' => User::where('tenant_id', $tenant->id)
                ->select('role', DB::raw('count(*) as count'))
                ->groupBy('role')
                ->pluck('count', 'role')
                ->toArray(),
        ];

        // Team Analytics
        $teamStats = [
            'total' => Team::where('tenant_id', $tenant->id)->count(),
            'active' => Team::where('tenant_id', $tenant->id)->where('is_active', true)->count(),
            'avg_members' => round(Team::where('tenant_id', $tenant->id)->avg('member_count'), 1),
            'largest_team' => Team::where('tenant_id', $tenant->id)
                ->orderBy('member_count', 'desc')
                ->first(),
        ];

        // Idea Analytics
        $ideaStats = [
            'total' => Idea::where('tenant_id', $tenant->id)->count(),
            'pending' => Idea::where('tenant_id', $tenant->id)->where('status', 'pending')->count(),
            'approved' => Idea::where('tenant_id', $tenant->id)->where('status', 'approved')->count(),
            'implemented' => Idea::where('tenant_id', $tenant->id)->where('status', 'implemented')->count(),
            'this_month' => Idea::where('tenant_id', $tenant->id)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'avg_pain_score' => round(Idea::where('tenant_id', $tenant->id)->avg('pain_score'), 1),
            'total_votes' => IdeaVote::whereHas('idea', function($q) use($tenant) {
                $q->where('tenant_id', $tenant->id);
            })->count(),
            'total_comments' => IdeaComment::whereHas('idea', function($q) use($tenant) {
                $q->where('tenant_id', $tenant->id);
            })->count(),
        ];

        // Top Ideas
        $topIdeas = Idea::where('tenant_id', $tenant->id)
            ->with(['user', 'team'])
            ->orderBy('votes', 'desc')
            ->limit(10)
            ->get();

        // Top Contributors
        $topContributors = User::where('tenant_id', $tenant->id)
            ->withCount(['ideas', 'votes', 'comments'])
            ->orderBy('ideas_count', 'desc')
            ->limit(10)
            ->get();

        // Recent Activity (last 30 days)
        $recentActivity = [
            'ideas_trend' => Idea::where('tenant_id', $tenant->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'users_trend' => User::where('tenant_id', $tenant->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return view('tenant.admin.analytics', compact(
            'tenant',
            'userStats',
            'teamStats',
            'ideaStats',
            'topIdeas',
            'topContributors',
            'recentActivity'
        ));
    }
}