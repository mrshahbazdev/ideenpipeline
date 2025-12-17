<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\{Team, Tenant, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TeamsController extends Controller
{
    /**
     * Show teams list (Admin only)
     */
    public function index(string $tenantId): View
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can manage teams.');
        }

        $tenant = Tenant::findOrFail($tenantId);
        
        $teams = Team::where('tenant_id', $tenant->id)
            ->withCount('members')
            ->with('creator')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_teams' => Team::where('tenant_id', $tenant->id)->count(),
            'active_teams' => Team::where('tenant_id', $tenant->id)->where('is_active', true)->count(),
            'total_members' => User::where('tenant_id', $tenant->id)->count(),
        ];

        return view('tenant.teams.index', compact('tenant', 'teams', 'stats'));
    }

    /**
     * Show create team form
     */
    public function create(string $tenantId): View
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can create teams.');
        }

        $tenant = Tenant::findOrFail($tenantId);
        
        $availableUsers = User::where('tenant_id', $tenant->id)
            ->where('id', '!=', Auth::id())
            ->get();

        return view('tenant.teams.create', compact('tenant', 'availableUsers'));
    }

    /**
     * Store new team
     */
    public function store(Request $request, string $tenantId): RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can create teams.');
        }

        $tenant = Tenant::findOrFail($tenantId);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-F]{6}$/i'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
        ]);

        // Create team
        $team = Team::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'created_by' => Auth::id(),
            'is_active' => true,
        ]);

        // Add members
        if ($request->filled('members')) {
            foreach ($request->members as $userId) {
                $user = User::withoutGlobalScope('tenant')
                    ->where('tenant_id', $tenant->id)
                    ->find($userId);
                    
                if ($user) {
                    $team->addMember($user);
                }
            }
        }

        \Log::info('Team created', [
            'tenant_id' => $tenant->id,
            'team_id' => $team->id,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('tenant.teams.index', ['tenantId' => $tenantId])
            ->with('success', 'Team "' . $team->name . '" created successfully!');
    }

    /**
     * Show single team
     */
    public function show(string $tenantId, Team $team): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        // Load relationships
        $team->load(['members', 'creator']);

        return view('tenant.teams.show', compact('tenant', 'team'));
    }

    /**
     * Show edit form
     */
    public function edit(string $tenantId, Team $team): View
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can edit teams.');
        }

        $tenant = Tenant::findOrFail($tenantId);
        
        $availableUsers = User::where('tenant_id', $tenant->id)
            ->where('id', '!=', Auth::id())
            ->get();

        return view('tenant.teams.edit', compact('tenant', 'team', 'availableUsers'));
    }

    /**
     * Update team
     */
    public function update(Request $request, string $tenantId, Team $team): RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can update teams.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-F]{6}$/i'],
            'is_active' => ['boolean'],
        ]);

        $team->update($request->only(['name', 'description', 'color', 'is_active']));

        return redirect()
            ->route('tenant.teams.show', ['tenantId' => $tenantId, 'team' => $team->id])
            ->with('success', 'Team updated successfully!');
    }

    /**
     * Delete team
     */
    public function destroy(string $tenantId, Team $team): RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can delete teams.');
        }

        $teamName = $team->name;
        $team->delete();

        return redirect()
            ->route('tenant.teams.index', ['tenantId' => $tenantId])
            ->with('success', 'Team "' . $teamName . '" deleted successfully!');
    }

    /**
     * Add member to team
     */
    public function addMember(Request $request, string $tenantId, Team $team): RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can add team members.');
        }

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $tenant = Tenant::findOrFail($tenantId);
        
        $user = User::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->findOrFail($request->user_id);

        if ($team->hasMember($user)) {
            return back()->with('error', $user->name . ' is already in this team.');
        }

        $team->addMember($user);

        return back()->with('success', $user->name . ' added to team successfully!');
    }

    /**
     * Remove member from team
     */
    public function removeMember(string $tenantId, Team $team, User $user): RedirectResponse
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only admins can remove team members.');
        }

        $team->removeMember($user);

        return back()->with('success', $user->name . ' removed from team successfully!');
    }
}