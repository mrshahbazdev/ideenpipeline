<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdeaVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'idea_id',
        'user_id',
        'vote_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Vote belongs to idea
     */
    public function idea()
    {
        return $this->belongsTo(Idea::class);
    }

    /**
     * Relationship: Vote belongs to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}