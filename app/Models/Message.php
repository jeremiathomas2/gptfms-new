<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'group_id',
        'content',
        'type',
        'attachments',
        'is_read',
        'read_at',
        'message_id',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeBetweenUsers($query, $userId1, $userId2)
    {
        return $query->where(function ($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId1)->where('receiver_id', $userId2);
        })->orWhere(function ($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId2)->where('receiver_id', $userId1);
        });
    }

    public function scopeInGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
