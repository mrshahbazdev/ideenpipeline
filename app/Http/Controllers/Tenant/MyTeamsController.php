<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\{Tenant, Team};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MyTeamsController extends Controller
{
    /**
     * Show user's teams
     */
    public function index(string $tenantId): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Get user's teams
        $myTeams = $user->teams()
            ->where('teams.tenant_id', $tenant->id)
            ->withCount('ideas')
            ->with('creator')
            ->orderBy('team_user.joined_at', 'desc')
            ->get();

        // Get available teams (not a member of)
        $availableTeams = Team::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->whereDoesntHave('members', function($query) use($user) {
                $query->where('user_id', $user->id);
            })
            ->withCount('members')
            ->get();

        // Get current active team
        $currentTeamId = session('current_team_id');
        $currentTeam = $currentTeamId ? Team::find($currentTeamId) : $myTeams->first();

        return view('tenant.my-teams.index', compact(
            'tenant',
            'user',
            'myTeams',
            'availableTeams',
            'currentTeam'
        ));
    }

    /**
     * Join a team
     */
    public function join(Request $request, string $tenantId, Team $team): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Check if team belongs to this tenant
        if ($team->tenant_id !== $tenant->id) {
            abort(403, 'Team not found in this tenant');
        }

        // Check if already a member
        if ($team->hasMember($user)) {
            return back()->with('error', 'You are already a member of this team.');
        }

        // Add member
        $team->addMember($user);

        // Set as active team if user has no active team
        if (!session('current_team_id')) {
            session(['current_team_id' => $team->id]);
        }

        \Log::info('User joined team', [
            'user_id' => $user->id,
            'team_id' => $team->id,
            'tenant_id' => $tenant->id,
        ]);

        return back()->with('success', "Successfully joined team: {$team->name}");
    }

    /**
     * Leave a team
     */
    public function leave(Request $request, string $tenantId, Team $team): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Check if team belongs to this tenant
        if ($team->tenant_id !== $tenant->id) {
            abort(403, 'Team not found in this tenant');
        }

        // Check if member
        if (!$team->hasMember($user)) {
            return back()->with('error', 'You are not a member of this team.');
        }

        // Check if this is the last member
        if ($team->member_count <= 1) {
            return back()->with('error', 'Cannot leave - you are the last member. Team needs at least one member.');
        }

        // Remove member
        $team->removeMember($user);

        // Clear active team if leaving current team
        if (session('current_team_id') == $team->id) {
            session()->forget('current_team_id');
            
            // Set to first remaining team if any
            $remainingTeam = $user->teams()->where('teams.tenant_id', $tenant->id)->first();
            if ($remainingTeam) {
                session(['current_team_id' => $remainingTeam->id]);
            }
        }

        \Log::info('User left team', [
            'user_id' => $user->id,
            'team_id' => $team->id,
            'tenant_id' => $tenant->id,
        ]);

        return back()->with('success', "Successfully left team: {$team->name}");
    }

    /**
     * Switch active team
     */
    public function switch(Request $request, string $tenantId, Team $team): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Check if team belongs to this tenant
        if ($team->tenant_id !== $tenant->id) {
            abort(403, 'Team not found in this tenant');
        }

        // Check if member
        if (!$team->hasMember($user)) {
            return back()->with('error', 'You are not a member of this team.');
        }

        // Switch team
        session(['current_team_id' => $team->id]);

        \Log::info('User switched team', [
            'user_id' => $user->id,
            'team_id' => $team->id,
            'tenant_id' => $tenant->id,
        ]);

        return back()->with('success', "Switched to team: {$team->name}");
    }
}