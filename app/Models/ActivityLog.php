<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper method to get subject model
    public function subject()
    {
        if ($this->subject_type && $this->subject_id) {
            $modelClass = 'App\\Models\\' . $this->subject_type;
            if (class_exists($modelClass)) {
                return $modelClass::find($this->subject_id);
            }
        }
        return null;
    }

    // Scopes
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBySubject($query, string $subjectType, $subjectId)
    {
        return $query->where('subject_type', $subjectType)
            ->where('subject_id', $subjectId);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
