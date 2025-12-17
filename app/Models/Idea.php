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
            $idea->calculatePriorities();
        });
    }

    /**
     * Calculate priority scores
     * Priority 1 = Pain Score (0-10)
     * Priority 2 = Pain Score / (Cost * Duration in days)
     */
    public function calculatePriorities()
    {
        // Priority 1: Just the pain score
        $this->priority_1 = $this->pain_score ?? 0;

        // Priority 2: Pain / (Cost * Duration)
        if ($this->pain_score && $this->cost_estimate && $this->duration_estimate) {
            $durationDays = $this->parseDuration($this->duration_estimate);
            $cost = (float) $this->cost_estimate;
            
            if ($durationDays > 0 && $cost > 0) {
                $this->priority_2 = round($this->pain_score / ($cost * $durationDays), 2);
            }
        }
    }

    /**
     * Parse duration string to days
     */
    private function parseDuration(string $duration): int
    {
        $duration = strtolower(trim($duration));
        
        if (preg_match('/(\d+)\s*(day|days)/i', $duration, $matches)) {
            return (int) $matches[1];
        }
        
        if (preg_match('/(\d+)\s*(week|weeks)/i', $duration, $matches)) {
            return (int) $matches[1] * 7;
        }
        
        if (preg_match('/(\d+)\s*(month|months)/i', $duration, $matches)) {
            return (int) $matches[1] * 30;
        }
        
        return 1; // Default 1 day
    }

    // ... rest of relationships and methods from previous Idea model
    
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

    /**
     * Get pain level label
     */
    public function getPainLabel(): string
    {
        if ($this->pain_score >= 8) return 'Critical';
        if ($this->pain_score >= 6) return 'High';
        if ($this->pain_score >= 4) return 'Medium';
        if ($this->pain_score >= 2) return 'Low';
        return 'Minimal';
    }

    /**
     * Get pain color
     */
    public function getPainColor(): string
    {
        if ($this->pain_score >= 8) return 'text-red-600';
        if ($this->pain_score >= 6) return 'text-orange-600';
        if ($this->pain_score >= 4) return 'text-yellow-600';
        return 'text-green-600';
    }
}