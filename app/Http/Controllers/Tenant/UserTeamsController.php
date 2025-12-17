<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\{Team, Tenant};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserTeamsController extends Controller
{
    /**
     * Show user's teams
     */
    public function index(string $tenantId): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Get all teams in this tenant
        $allTeams = Team::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->withCount('members')
            ->with('creator')
            ->latest()
            ->get();

        // Get user's teams
        $myTeams = $user->teams()->where('teams.tenant_id', $tenant->id)->get();
        
        // Get current active team from session
        $currentTeamId = session('current_team_id');
        $currentTeam = $currentTeamId ? Team::find($currentTeamId) : $myTeams->first();

        // Teams user can join (not already a member)
        $availableTeams = $allTeams->filter(function($team) use($myTeams) {
            return !$myTeams->contains('id', $team->id);
        });

        return view('tenant.my-teams.index', compact(
            'tenant',
            'user',
            'myTeams',
            'availableTeams',
            'currentTeam',
            'allTeams'
        ));
    }

    /**
     * Join a team
     */
    public function joinTeam(Request $request, string $tenantId, Team $team): RedirectResponse
    {
        $user = Auth::user();

        if ($team->hasMember($user)) {
            return back()->with('error', 'You are already a member of this team.');
        }

        $team->addMember($user);

        \Log::info('User joined team', [
            'user_id' => $user->id,
            'team_id' => $team->id,
            'tenant_id' => $tenantId,
        ]);

        return back()->with('success', "You've successfully joined {$team->name}!");
    }

    /**
     * Leave a team
     */
    public function leaveTeam(Request $request, string $tenantId, Team $team): RedirectResponse
    {
        $user = Auth::user();

        if (!$team->hasMember($user)) {
            return back()->with('error', 'You are not a member of this team.');
        }

        $team->removeMember($user);

        // If leaving current active team, clear session
        if (session('current_team_id') == $team->id) {
            session()->forget('current_team_id');
        }

        \Log::info('User left team', [
            'user_id' => $user->id,
            'team_id' => $team->id,
            'tenant_id' => $tenantId,
        ]);

        return back()->with('success', "You've left {$team->name}.");
    }

    /**
     * Switch active team
     */
    public function switchTeam(string $tenantId, Team $team): RedirectResponse
    {
        $user = Auth::user();

        if (!$team->hasMember($user)) {
            return back()->with('error', 'You are not a member of this team.');
        }

        session(['current_team_id' => $team->id]);

        \Log::info('User switched team', [
            'user_id' => $user->id,
            'team_id' => $team->id,
            'team_name' => $team->name,
        ]);

        return back()->with('success', "Switched to {$team->name}");
    }
}