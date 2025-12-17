<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\{Idea, Team, Tenant};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class IdeasController extends Controller
{
    /**
     * Show ideas list (requires active team)
     */
    public function index(string $tenantId): View|RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Check if user has any teams
        $userTeams = $user->teams()->where('teams.tenant_id', $tenant->id)->get();

        if ($userTeams->isEmpty()) {
            return redirect()
                ->route('tenant.my-teams', ['tenantId' => $tenantId])
                ->with('error', 'You must join a team first before accessing ideas.');
        }

        // Get current active team
        $currentTeamId = session('current_team_id');
        $currentTeam = $currentTeamId 
            ? Team::find($currentTeamId) 
            : $userTeams->first();

        // If no active team is set, set the first one
        if (!$currentTeam) {
            $currentTeam = $userTeams->first();
            session(['current_team_id' => $currentTeam->id]);
        }

        // Verify user is member of current team
        if (!$currentTeam->hasMember($user)) {
            session()->forget('current_team_id');
            return redirect()
                ->route('tenant.my-teams', ['tenantId' => $tenantId])
                ->with('error', 'You are not a member of the selected team. Please switch to a team you belong to.');
        }

        // Get ideas for current team
        $ideas = Idea::where('team_id', $currentTeam->id)
            ->with(['user', 'team'])
            ->withCount('votes')
            ->latest()
            ->paginate(12);

        $stats = [
            'total_ideas' => Idea::where('team_id', $currentTeam->id)->count(),
            'pending' => Idea::where('team_id', $currentTeam->id)->where('status', 'pending')->count(),
            'approved' => Idea::where('team_id', $currentTeam->id)->where('status', 'approved')->count(),
            'my_ideas' => Idea::where('team_id', $currentTeam->id)->where('user_id', $user->id)->count(),
        ];

        return view('tenant.ideas.index', compact(
            'tenant',
            'user',
            'currentTeam',
            'userTeams',
            'ideas',
            'stats'
        ));
    }

    /**
     * Show create idea form
     */
    public function create(string $tenantId): View|RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Check current team
        $currentTeamId = session('current_team_id');
        if (!$currentTeamId) {
            return redirect()
                ->route('tenant.my-teams', ['tenantId' => $tenantId])
                ->with('error', 'Please select a team first.');
        }

        $currentTeam = Team::find($currentTeamId);
        
        if (!$currentTeam || !$currentTeam->hasMember($user)) {
            return redirect()
                ->route('tenant.my-teams', ['tenantId' => $tenantId])
                ->with('error', 'Invalid team selected.');
        }

        return view('tenant.ideas.create', compact('tenant', 'user', 'currentTeam'));
    }

    /**
     * Store new idea
     */
    public function store(Request $request, string $tenantId): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Validate current team
        $currentTeamId = session('current_team_id');
        if (!$currentTeamId) {
            return redirect()
                ->route('tenant.my-teams', ['tenantId' => $tenantId])
                ->with('error', 'Please select a team first.');
        }

        $currentTeam = Team::findOrFail($currentTeamId);

        if (!$currentTeam->hasMember($user)) {
            return back()->with('error', 'You are not a member of this team.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'tags' => ['nullable', 'string'],
        ]);

        // Process tags
        $tags = $validated['tags'] 
            ? array_map('trim', explode(',', $validated['tags'])) 
            : [];

        // Create idea
        $idea = Idea::create([
            'tenant_id' => $tenant->id,
            'team_id' => $currentTeam->id,
            'user_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'tags' => $tags,
            'status' => 'pending',
        ]);

        \Log::info('Idea created', [
            'idea_id' => $idea->id,
            'team_id' => $currentTeam->id,
            'user_id' => $user->id,
        ]);

        return redirect()
            ->route('tenant.ideas.index', ['tenantId' => $tenantId])
            ->with('success', 'Idea submitted successfully!');
    }

    /**
     * Show single idea
     */
    public function show(string $tenantId, Idea $idea): View|RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Check if user is member of idea's team
        if (!$idea->team->hasMember($user)) {
            return redirect()
                ->route('tenant.ideas.index', ['tenantId' => $tenantId])
                ->with('error', 'You do not have access to this idea.');
        }

        $idea->load(['user', 'team', 'votes.user']);

        return view('tenant.ideas.show', compact('tenant', 'user', 'idea'));
    }
}