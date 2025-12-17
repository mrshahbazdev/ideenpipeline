<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'color',
        'member_count',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'member_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Global scope - Only show teams from current tenant
     */
    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenant = request()->attributes->get('tenant');
            
            if ($tenant) {
                $builder->where('tenant_id', $tenant->id);
            }
        });
    }

    /**
     * Relationship: Team belongs to Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relationship: Team has many members (users)
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withTimestamps()
            ->withPivot('joined_at')
            ->using(TeamUserPivot::class);
    }

    /**
     * Alias for members (for compatibility)
     */
    public function users()
    {
        return $this->members();
    }

    /**
     * Relationship: Team has many ideas
     */
    public function ideas()
    {
        return $this->hasMany(Idea::class);
    }

    /**
     * Relationship: Team creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Add member to team
     */
    public function addMember(User $user): void
    {
        if (!$this->hasMember($user)) {
            $this->members()->attach($user->id, [
                'joined_at' => now()
            ]);
            $this->updateMemberCount();
            
            \Log::info('Member added to team', [
                'team_id' => $this->id,
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Remove member from team
     */
    public function removeMember(User $user): void
    {
        if ($this->hasMember($user)) {
            $this->members()->detach($user->id);
            $this->updateMemberCount();
            
            \Log::info('Member removed from team', [
                'team_id' => $this->id,
                'user_id' => $user->id,
            ]);
        }
    }

    /**
     * Update member count
     */
    public function updateMemberCount(): void
    {
        $this->withoutEvents(function () {
            $count = $this->members()->count();
            $this->update([
                'member_count' => $count
            ]);
        });
    }

    /**
     * Check if user is member
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Get team avatar color class
     */
    public function getColorClass(): string
    {
        return "bg-[{$this->color}]";
    }

    /**
     * Get team status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return $this->is_active 
            ? 'bg-green-100 text-green-800 border-green-300'
            : 'bg-gray-100 text-gray-800 border-gray-300';
    }

    /**
     * Get team size label
     */
    public function getSizeLabel(): string
    {
        if ($this->member_count >= 20) return 'Large';
        if ($this->member_count >= 10) return 'Medium';
        if ($this->member_count >= 5) return 'Small';
        return 'Tiny';
    }

    /**
     * Get ideas count
     */
    public function getIdeasCountAttribute(): int
    {
        return $this->ideas()->count();
    }

    /**
     * Get active ideas count
     */
    public function getActiveIdeasCountAttribute(): int
    {
        return $this->ideas()
            ->whereIn('status', ['pending', 'in-review', 'approved'])
            ->count();
    }

    /**
     * Get implemented ideas count
     */
    public function getImplementedIdeasCountAttribute(): int
    {
        return $this->ideas()
            ->where('status', 'implemented')
            ->count();
    }

    /**
     * Get total votes for team's ideas
     */
    public function getTotalVotesAttribute(): int
    {
        return $this->ideas()->sum('votes');
    }

    /**
     * Scope: Active teams only
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Inactive teams only
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope: Teams with at least X members
     */
    public function scopeMinMembers(Builder $query, int $count): Builder
    {
        return $query->where('member_count', '>=', $count);
    }

    /**
     * Scope: Teams created in the last X days
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get formatted member count
     */
    public function getMemberCountFormattedAttribute(): string
    {
        $count = $this->member_count;
        
        if ($count >= 1000) {
            return round($count / 1000, 1) . 'k';
        }
        
        return (string) $count;
    }

    /**
     * Get team activity score (based on ideas and votes)
     */
    public function getActivityScoreAttribute(): int
    {
        $ideasCount = $this->ideas()->count();
        $votesCount = $this->ideas()->sum('votes');
        $commentsCount = \DB::table('idea_comments')
            ->join('ideas', 'idea_comments.idea_id', '=', 'ideas.id')
            ->where('ideas.team_id', $this->id)
            ->count();
        
        // Simple scoring: ideas * 10 + votes * 2 + comments * 1
        return ($ideasCount * 10) + ($votesCount * 2) + $commentsCount;
    }

    /**
     * Get team performance rating (1-5 stars)
     */
    public function getPerformanceRatingAttribute(): int
    {
        $activityScore = $this->activity_score;
        
        if ($activityScore >= 100) return 5;
        if ($activityScore >= 50) return 4;
        if ($activityScore >= 20) return 3;
        if ($activityScore >= 5) return 2;
        return 1;
    }

    /**
     * Check if team is full (optional max capacity)
     */
    public function isFull(int $maxCapacity = 100): bool
    {
        return $this->member_count >= $maxCapacity;
    }

    /**
     * Get team join date for specific user
     */
    public function getMemberJoinDate(User $user): ?Carbon
    {
        $pivot = $this->members()
            ->where('user_id', $user->id)
            ->first();
        
        return $pivot ? $pivot->pivot->joined_at : null;
    }

    /**
     * Get most active member (by ideas count)
     */
    public function getMostActiveMember(): ?User
    {
        return $this->members()
            ->withCount(['ideas' => function($query) {
                $query->where('team_id', $this->id);
            }])
            ->orderBy('ideas_count', 'desc')
            ->first();
    }

    /**
     * Get recent members (joined in last X days)
     */
    public function getRecentMembers(int $days = 30)
    {
        return $this->members()
            ->wherePivot('joined_at', '>=', now()->subDays($days))
            ->orderBy('team_user.joined_at', 'desc')
            ->get();
    }

    /**
     * Deactivate team
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
        
        \Log::info('Team deactivated', [
            'team_id' => $this->id,
            'team_name' => $this->name,
        ]);
    }

    /**
     * Activate team
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
        
        \Log::info('Team activated', [
            'team_id' => $this->id,
            'team_name' => $this->name,
        ]);
    }

    /**
     * Get team initials for avatar
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        
        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * Get member roles distribution
     */
    public function getMemberRolesDistribution(): array
    {
        return $this->members()
            ->select('role', \DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();
    }

    /**
     * Export team data
     */
    public function toExport(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'member_count' => $this->member_count,
            'ideas_count' => $this->ideas_count,
            'active_ideas_count' => $this->active_ideas_count,
            'implemented_ideas_count' => $this->implemented_ideas_count,
            'total_votes' => $this->total_votes,
            'activity_score' => $this->activity_score,
            'performance_rating' => $this->performance_rating,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}