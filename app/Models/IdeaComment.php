<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdeaComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'idea_id',
        'user_id',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Comment belongs to idea
     */
    public function idea()
    {
        return $this->belongsTo(Idea::class);
    }

    /**
     * Relationship: Comment belongs to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}