<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
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

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Tenant relationship
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Boot method - auto scope by tenant
     */
    protected static function boot()
    {
        parent::boot();

        // Global scope for tenant isolation
        static::addGlobalScope('tenant', function (Builder $query) {
            if (session()->has('tenant_id')) {
                $query->where('tenant_id', session('tenant_id'));
            }
        });

        // Auto-assign tenant_id when creating
        static::creating(function ($model) {
            if (session()->has('tenant_id') && !$model->tenant_id) {
                $model->tenant_id = session('tenant_id');
            }
        });
    }
}