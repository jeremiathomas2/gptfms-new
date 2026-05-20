<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'project_id',
        'assigned_to',
        'created_by',
        'status',
        'priority',
        'estimated_hours',
        'actual_hours',
        'due_date',
        'completed_at',
        'dependencies',
        'tags',
        'order',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'dependencies' => 'array',
        'tags' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper methods
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    public function getDaysRemaining(): ?int
    {
        return $this->due_date ? now()->diffInDays($this->due_date, false) : null;
    }

    public function canBeCompleted(): bool
    {
        if (empty($this->dependencies)) {
            return true;
        }

        $dependencyIds = $this->dependencies;
        if (!is_array($dependencyIds)) {
            return true;
        }

        $completedDependencies = Task::whereIn('id', $dependencyIds)
            ->where('status', 'completed')
            ->count();

        return $completedDependencies === count($dependencyIds);
    }

    public function getDependencyTasks(): \Illuminate\Database\Eloquent\Collection
    {
        if (empty($this->dependencies)) {
            return collect();
        }

        return Task::whereIn('id', $this->dependencies)->get();
    }

    // Scopes
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', ['completed']);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }
}
