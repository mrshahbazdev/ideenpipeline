<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Idea extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'team_id',
        'user_id',
        'title',
        'problem_short',
        'goal',
        'description',
        'solution',
        'pain_score',
        'cost_estimate',
        'duration_estimate',
        'duration_days',
        'status',
        'priority',
        'priority_1',
        'priority_2',
        'tags',
        'votes',
        'in_implementation',
        'implementation_date',
        'submitter_email',
    ];

    protected $casts = [
        'tags' => 'array',
        'votes' => 'integer',
        'pain_score' => 'integer',
        'duration_days' => 'integer',
        'cost_estimate' => 'decimal:2',
        'priority_1' => 'decimal:2',
        'priority_2' => 'decimal:2',
        'in_implementation' => 'boolean',
        'implementation_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method - Auto-calculate priorities
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($idea) {
            // Parse duration to days if duration_estimate changed
            if ($idea->isDirty('duration_estimate')) {
                $idea->duration_days = $idea->parseDurationToDays($idea->duration_estimate);
            }
            
            // Calculate priorities
            $idea->calculatePriorities();
        });
    }

    /**
     * Calculate priority scores
     * NEW FORMULA:
     * Prio 1 = (Kosten / 100) + Dauer
     * Prio 2 = Prio 1 / Schmerz
     */
    public function calculatePriorities()
    {
        $cost = (float) ($this->cost_estimate ?? 0);
        $duration = (int) ($this->duration_days ?? 0);
        $pain = (int) ($this->pain_score ?? 1); // Prevent division by zero

        // Prio 1 = (Cost / 100) + Duration
        if ($cost > 0 || $duration > 0) {
            $this->priority_1 = round(($cost / 100) + $duration, 2);
        } else {
            $this->priority_1 = 0;
        }

        // Prio 2 = Prio 1 / Pain
        if ($this->priority_1 > 0 && $pain > 0) {
            $this->priority_2 = round($this->priority_1 / $pain, 2);
        } else {
            $this->priority_2 = 0;
        }
    }

    /**
     * Parse duration string to days
     */
    public function parseDurationToDays(?string $duration): int
    {
        if (!$duration) return 0;
        
        $duration = strtolower(trim($duration));
        
        // Try to extract number and unit
        if (preg_match('/(\d+)\s*(tag|tage|day|days|d)/i', $duration, $matches)) {
            return (int) $matches[1];
        }
        
        if (preg_match('/(\d+)\s*(woche|wochen|week|weeks|w)/i', $duration, $matches)) {
            return (int) $matches[1] * 7;
        }
        
        if (preg_match('/(\d+)\s*(monat|monate|month|months|m)/i', $duration, $matches)) {
            return (int) $matches[1] * 30;
        }
        
        // If just a number, assume days
        if (is_numeric($duration)) {
            return (int) $duration;
        }
        
        return 0;
    }

    /**
     * Global scope - Filter by tenant
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

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function votes()
    {
        return $this->hasMany(IdeaVote::class);
    }

    public function hasVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if user can edit basic fields (creator only)
     */
    public function canEditBasic(User $user): bool
    {
        return $user->id === $this->user_id || $user->isAdmin();
    }

    /**
     * Check if user can edit work-bee fields
     * (Schmerz, Prio 1, Prio 2, Umsetzung)
     */
    public function canEditWorkBee(User $user): bool
    {
        return $user->isWorkBee() || $user->isAdmin();
    }

    /**
     * Check if user can edit developer fields
     * (Lösung, Dauer, Kosten)
     */
    public function canEditDeveloper(User $user): bool
    {
        return $user->isDeveloper() || $user->isAdmin();
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'in-review' => 'bg-blue-100 text-blue-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'implemented' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPriorityBadgeClass(): string
    {
        return match($this->priority) {
            'low' => 'bg-gray-100 text-gray-800',
            'medium' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPainLabel(): string
    {
        if ($this->pain_score >= 8) return 'Critical';
        if ($this->pain_score >= 6) return 'High';
        if ($this->pain_score >= 4) return 'Medium';
        if ($this->pain_score >= 2) return 'Low';
        return 'Minimal';
    }

    public function getPainColor(): string
    {
        if ($this->pain_score >= 8) return 'text-red-600';
        if ($this->pain_score >= 6) return 'text-orange-600';
        if ($this->pain_score >= 4) return 'text-yellow-600';
        return 'text-green-600';
    }
    /**
     * Relationship: Idea has many comments
     */
    public function comments()
    {
        return $this->hasMany(IdeaComment::class)->latest();
    }

    /**
     * Get comments count
     */
    public function getCommentsCountAttribute(): int
    {
        return $this->comments()->count();
    }
}