<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'id',
        'platform_subscription_id',
        'platform_user_id',
        'admin_name',
        'admin_email',
        'package_name',
        'subdomain',
        'domain',
        'starts_at',
        'expires_at',
        'status',
        'metadata',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Tenant users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'tenant_id');
    }

    /**
     * Check if tenant is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->expires_at === null || $this->expires_at > now());
    }

    /**
     * Check if tenant is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at <= now();
    }

    /**
     * Get days remaining
     */
    public function daysRemaining(): int
    {
        if ($this->expires_at === null) {
            return 0;
        }

        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->expires_at);
    }
}