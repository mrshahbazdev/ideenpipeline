<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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
}