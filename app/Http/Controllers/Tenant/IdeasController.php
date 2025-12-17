<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\{Idea, Team, Tenant};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Models\IdeaComment;

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
     * Store comment
     */
    public function storeComment(Request $request, string $tenantId, Idea $idea): JsonResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Check if user is member of idea's team
        if (!$idea->team->hasMember($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You must be a team member to comment.',
            ], 403);
        }

        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $comment = $idea->comments()->create([
                'user_id' => $user->id,
                'comment' => $validated['comment'],
            ]);

            $comment->load('user');

            \Log::info('Comment added', [
                'idea_id' => $idea->id,
                'user_id' => $user->id,
                'comment_id' => $comment->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'user' => [
                        'name' => $comment->user->name,
                        'role' => $comment->user->role,
                        'avatar' => strtoupper(substr($comment->user->name, 0, 1)),
                    ],
                    'created_at' => $comment->created_at->diffForHumans(),
                    'created_at_full' => $comment->created_at->format('M d, Y \a\t g:i A'),
                ],
                'commentsCount' => $idea->comments()->count(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Comment failed', [
                'idea_id' => $idea->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment',
            ], 500);
        }
    }

    /**
     * Delete comment
     */
    public function deleteComment(Request $request, string $tenantId, Idea $idea, IdeaComment $comment): JsonResponse
    {
        $user = Auth::user();

        // Check permissions (owner or admin)
        if ($comment->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete this comment.',
            ], 403);
        }

        try {
            $comment->delete();

            \Log::info('Comment deleted', [
                'comment_id' => $comment->id,
                'idea_id' => $idea->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted',
                'commentsCount' => $idea->comments()->count(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Comment deletion failed', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment',
            ], 500);
        }
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
     * Toggle vote on idea
     */
    public function vote(Request $request, string $tenantId, Idea $idea): JsonResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Check if user is member of idea's team
        if (!$idea->team->hasMember($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You must be a team member to vote.',
            ], 403);
        }

        try {
            // Check if already voted
            $existingVote = $idea->votes()->where('user_id', $user->id)->first();

            if ($existingVote) {
                // Remove vote (unvote)
                $existingVote->delete();
                $idea->decrement('votes');
                $hasVoted = false;
                $message = 'Vote removed';
            } else {
                // Add vote
                $idea->votes()->create([
                    'user_id' => $user->id,
                    'vote_type' => 'up',
                ]);
                $idea->increment('votes');
                $hasVoted = true;
                $message = 'Vote added';
            }

            // Refresh idea to get updated vote count
            $idea->refresh();

            \Log::info('Vote toggled', [
                'idea_id' => $idea->id,
                'user_id' => $user->id,
                'action' => $hasVoted ? 'added' : 'removed',
                'total_votes' => $idea->votes,
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'hasVoted' => $hasVoted,
                'voteCount' => $idea->votes,
                'votersCount' => $idea->votes()->count(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Vote failed', [
                'idea_id' => $idea->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process vote',
            ], 500);
        }
    }

    /**
     * Quick status change (Admin only)
     */
    public function updateStatus(Request $request, string $tenantId, Idea $idea): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Admin only
        if (!$user->isAdmin()) {
            abort(403, 'Only admins can change status.');
        }

        // Check access
        if (!$idea->team->hasMember($user)) {
            abort(403, 'You do not have access to this idea.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,in-review,approved,rejected,implemented'],
        ]);

        $oldStatus = $idea->status;
        $idea->status = $validated['status'];
        $idea->save();

        \Log::info('Idea status changed', [
            'idea_id' => $idea->id,
            'old_status' => $oldStatus,
            'new_status' => $idea->status,
            'admin_id' => $user->id,
        ]);

        $statusMessages = [
            'approved' => 'Idea approved successfully!',
            'in-review' => 'Idea moved to review.',
            'rejected' => 'Idea rejected.',
            'implemented' => 'Idea marked as implemented!',
            'pending' => 'Idea moved back to pending.',
        ];

        return back()->with('success', $statusMessages[$idea->status] ?? 'Status updated.');
    }
    /**
     * Show edit form
     */
    public function edit(string $tenantId, Idea $idea): View
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Check access
        if (!$idea->team->hasMember($user)) {
            abort(403, 'You do not have access to this idea.');
        }

        return view('tenant.ideas.edit', compact('tenant', 'user', 'idea'));
    }

    /**
     * Update idea with role-based permissions
     */
    public function update(Request $request, string $tenantId, Idea $idea): RedirectResponse
    {
        $tenant = Tenant::findOrFail($tenantId);
        $user = Auth::user();

        // Check access
        if (!$idea->team->hasMember($user)) {
            abort(403, 'You do not have access to this idea.');
        }

        // Validate based on user role
        $rules = [];

        // Basic fields (creator only, unless admin)
        if ($idea->canEditBasic($user)) {
            $rules = array_merge($rules, [
                'title' => ['required', 'string', 'max:255'],
                'problem_short' => ['required', 'string', 'max:100'],
                'goal' => ['required', 'string', 'max:1000'],
                'description' => ['required', 'string', 'max:5000'],
                'priority' => ['required', 'in:low,medium,high,urgent'],
                'submitter_email' => ['required', 'email', 'max:255'],
            ]);
        }

        // Work-bee fields (Schmerz, Umsetzung)
        if ($idea->canEditWorkBee($user)) {
            $rules = array_merge($rules, [
                'pain_score' => ['nullable', 'integer', 'min:0', 'max:10'],
                'in_implementation' => ['nullable', 'boolean'],
                'implementation_date' => ['nullable', 'date'],
            ]);
        }

        // Developer fields (Lösung, Dauer, Kosten)
        if ($idea->canEditDeveloper($user)) {
            $rules = array_merge($rules, [
                'solution' => ['nullable', 'string', 'max:5000'],
                'cost_estimate' => ['nullable', 'numeric', 'min:0'],
                'duration_estimate' => ['nullable', 'string', 'max:50'],
            ]);
        }

        // Admin can edit status
        if ($user->isAdmin()) {
            $rules['status'] = ['nullable', 'in:pending,in-review,approved,rejected,implemented'];
        }

        $validated = $request->validate($rules);

        // Update only allowed fields
        if ($idea->canEditBasic($user)) {
            $idea->title = $validated['title'] ?? $idea->title;
            $idea->problem_short = $validated['problem_short'] ?? $idea->problem_short;
            $idea->goal = $validated['goal'] ?? $idea->goal;
            $idea->description = $validated['description'] ?? $idea->description;
            $idea->priority = $validated['priority'] ?? $idea->priority;
            $idea->submitter_email = $validated['submitter_email'] ?? $idea->submitter_email;
        }

        if ($idea->canEditWorkBee($user)) {
            if (isset($validated['pain_score'])) {
                $idea->pain_score = $validated['pain_score'];
            }
            if (isset($validated['in_implementation'])) {
                $idea->in_implementation = $validated['in_implementation'];
            }
            if (isset($validated['implementation_date'])) {
                $idea->implementation_date = $validated['implementation_date'];
            }
        }

        if ($idea->canEditDeveloper($user)) {
            if (isset($validated['solution'])) {
                $idea->solution = $validated['solution'];
            }
            if (isset($validated['cost_estimate'])) {
                $idea->cost_estimate = $validated['cost_estimate'];
            }
            if (isset($validated['duration_estimate'])) {
                $idea->duration_estimate = $validated['duration_estimate'];
            }
        }

        if ($user->isAdmin() && isset($validated['status'])) {
            $idea->status = $validated['status'];
        }

        // Save (priorities will auto-calculate)
        $idea->save();

        \Log::info('Idea updated', [
            'idea_id' => $idea->id,
            'user_id' => $user->id,
            'user_role' => $user->role,
            'priority_1' => $idea->priority_1,
            'priority_2' => $idea->priority_2,
        ]);

        return redirect()
            ->route('tenant.ideas.show', ['tenantId' => $tenantId, 'idea' => $idea->id])
            ->with('success', 'Idea updated successfully!');
    }
    /**
     * Show ideas in table view (admin/management view)
     */
    public function table(string $tenantId): View|RedirectResponse
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

        if (!$currentTeam) {
            $currentTeam = $userTeams->first();
            session(['current_team_id' => $currentTeam->id]);
        }

        // Verify user is member of current team
        if (!$currentTeam->hasMember($user)) {
            session()->forget('current_team_id');
            return redirect()
                ->route('tenant.my-teams', ['tenantId' => $tenantId])
                ->with('error', 'You are not a member of the selected team.');
        }

        // Get all ideas for current team with sorting
        $sortBy = request('sort', 'priority_1');
        $sortOrder = request('order', 'desc');

        $ideas = Idea::where('team_id', $currentTeam->id)
            ->with(['user', 'team'])
            ->orderBy($sortBy, $sortOrder)
            ->get();

        $stats = [
            'total_ideas' => $ideas->count(),
            'pending' => $ideas->where('status', 'pending')->count(),
            'approved' => $ideas->where('status', 'approved')->count(),
            'in_implementation' => $ideas->where('in_implementation', true)->count(),
            'avg_pain' => round($ideas->avg('pain_score'), 1),
            'total_cost' => $ideas->sum('cost_estimate'),
        ];

        return view('tenant.ideas.table', compact(
            'tenant',
            'user',
            'currentTeam',
            'userTeams',
            'ideas',
            'stats',
            'sortBy',
            'sortOrder'
        ));
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
            'problem_short' => ['required', 'string', 'max:100'],
            'goal' => ['required', 'string', 'max:1000'],
            'description' => ['required', 'string', 'max:5000'],
            'solution' => ['nullable', 'string', 'max:5000'],
            'pain_score' => ['required', 'integer', 'min:0', 'max:10'],
            'cost_estimate' => ['nullable', 'numeric', 'min:0'],
            'duration_estimate' => ['nullable', 'string', 'max:50'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'submitter_email' => ['required', 'email', 'max:255'],
        ]);

        // Create idea (priorities will be auto-calculated in model)
        $idea = Idea::create([
            'tenant_id' => $tenant->id,
            'team_id' => $currentTeam->id,
            'user_id' => $user->id,
            'title' => $validated['title'],
            'problem_short' => $validated['problem_short'],
            'goal' => $validated['goal'],
            'description' => $validated['description'],
            'solution' => $validated['solution'] ?? null,
            'pain_score' => $validated['pain_score'],
            'cost_estimate' => $validated['cost_estimate'] ?? null,
            'duration_estimate' => $validated['duration_estimate'] ?? null,
            'priority' => $validated['priority'],
            'submitter_email' => $validated['submitter_email'],
            'status' => 'pending',
        ]);

        \Log::info('Idea created', [
            'idea_id' => $idea->id,
            'team_id' => $currentTeam->id,
            'user_id' => $user->id,
            'pain_score' => $idea->pain_score,
            'priority_1' => $idea->priority_1,
            'priority_2' => $idea->priority_2,
        ]);

        return redirect()
            ->route('tenant.ideas.table', ['tenantId' => $tenantId])
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