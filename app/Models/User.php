<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tenant_id',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Existing relationships and methods...
    
    /**
     * Get user's initials for avatar
     */
    public function getInitialsAttribute(): string
    {
        $names = explode(' ', $this->name);
        if (count($names) >= 2) {
            return strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    /**
     * Get role color class
     */
    public function getRoleColorAttribute(): string
    {
        return match($this->role) {
            'admin' => 'bg-gradient-to-br from-red-500 to-pink-600',
            'developer' => 'bg-gradient-to-br from-purple-500 to-indigo-600',
            'work-bee' => 'bg-gradient-to-br from-green-500 to-emerald-600',
            default => 'bg-gradient-to-br from-blue-500 to-cyan-600',
        };
    }

    /**
     * Get role badge class
     */
    public function getRoleBadgeAttribute(): string
    {
        return match($this->role) {
            'admin' => 'bg-red-100 text-red-800 border-red-200',
            'developer' => 'bg-purple-100 text-purple-800 border-purple-200',
            'work-bee' => 'bg-green-100 text-green-800 border-green-200',
            default => 'bg-blue-100 text-blue-800 border-blue-200',
        };
    }
    /**
     * Relationship: User has many ideas
     */
    public function ideas()
    {
        return $this->hasMany(Idea::class);
    }

    /**
     * Relationship: User has many votes
     */
    public function votes()
    {
        return $this->hasMany(IdeaVote::class);
    }

    /**
     * Relationship: User has many comments
     */
    public function comments()
    {
        return $this->hasMany(IdeaComment::class);
    }
    /**
     * Global scope to filter users by tenant
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
     * Relationship: User belongs to Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is developer
     */
    public function isDeveloper(): bool
    {
        return $this->role === 'developer';
    }

    /**
     * Check if user is work-bee
     */
    public function isWorkBee(): bool
    {
        return $this->role === 'work-bee';
    }

    /**
     * Check if user is standard user
     */
    public function isStandard(): bool
    {
        return $this->role === 'standard';
    }

    /**
     * Get role badge color
     */
    public function getRoleBadgeClass(): string
    {
        return match($this->role) {
            'admin' => 'bg-red-100 text-red-800',
            'developer' => 'bg-purple-100 text-purple-800',
            'work-bee' => 'bg-green-100 text-green-800',
            'standard' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get role icon
     */
    public function getRoleIcon(): string
    {
        return match($this->role) {
            'admin' => 'fa-crown',
            'developer' => 'fa-code',
            'work-bee' => 'fa-user-friends',
            'standard' => 'fa-user',
            default => 'fa-user',
        };
    }
    /**
     * Relationship: User belongs to many teams
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user')
            ->withTimestamps()
            ->withPivot('joined_at');
    }

    /**
     * Check if user is in team
     */
    public function isInTeam(Team $team): bool
    {
        return $this->teams()->where('team_id', $team->id)->exists();
    }
}