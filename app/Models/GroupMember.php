<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'role',
        'status',
        'joined_at',
        'left_at',
        'contribution_score',
        'tasks_completed',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'contribution_score' => 'decimal:2',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'joined');
    }

    public function scopeLeaders($query)
    {
        return $query->where('role', 'leader');
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
