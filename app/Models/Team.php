<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

        // Auto-update member count when members added/removed
        static::saved(function ($team) {
            $team->updateMemberCount();
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
            ->withPivot('joined_at');
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
        if (!$this->members()->where('user_id', $user->id)->exists()) {
            $this->members()->attach($user->id, ['joined_at' => now()]);
            $this->updateMemberCount();
        }
    }

    /**
     * Remove member from team
     */
    public function removeMember(User $user): void
    {
        $this->members()->detach($user->id);
        $this->updateMemberCount();
    }

    /**
     * Update member count
     */
    public function updateMemberCount(): void
    {
        $this->withoutEvents(function () {
            $this->update([
                'member_count' => $this->members()->count()
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
}