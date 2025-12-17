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
        'description',
        'status',
        'priority',
        'tags',
        'votes',
    ];

    protected $casts = [
        'tags' => 'array',
        'votes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

    /**
     * Relationship: Idea belongs to team
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Relationship: Idea belongs to user (creator)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Idea has many votes
     */
    public function votes()
    {
        return $this->hasMany(IdeaVote::class);
    }

    /**
     * Check if user has voted
     */
    public function hasVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }

    /**
     * Get user's vote
     */
    public function getUserVote(User $user)
    {
        return $this->votes()->where('user_id', $user->id)->first();
    }

    /**
     * Get status badge color
     */
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

    /**
     * Get priority badge color
     */
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
     * Get status icon
     */
    public function getStatusIcon(): string
    {
        return match($this->status) {
            'pending' => 'fa-clock',
            'in-review' => 'fa-eye',
            'approved' => 'fa-check-circle',
            'rejected' => 'fa-times-circle',
            'implemented' => 'fa-rocket',
            default => 'fa-circle',
        };
    }
}