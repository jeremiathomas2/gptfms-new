<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    public const PHASES = [
        1 => 'Project Title',
        2 => 'Gather Requirement',
        3 => 'Analysis',
        4 => 'Designing',
        5 => 'Development and Testing',
        6 => 'Deployment',
    ];

    protected $fillable = [
        'title',
        'description',
        'supervisor_id',
        'group_id',
        'status',
        'priority',
        'start_date',
        'end_date',
        'progress_percentage',
        'requirements',
        'deliverables',
        'course_code',
        'max_grade',
        'final_grade',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'requirements' => 'array',
        'deliverables' => 'array',
        'max_grade' => 'decimal:2',
        'final_grade' => 'decimal:2',
    ];

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class)->orderBy('phase_number');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    public function peerEvaluations(): HasMany
    {
        return $this->hasMany(PeerEvaluation::class);
    }

    // Helper methods
    public function getCompletedTasksCount(): int
    {
        return $this->tasks()->where('status', 'completed')->count();
    }

    public function getTotalTasksCount(): int
    {
        return $this->tasks()->count();
    }

    public function getProgressByTasks(): float
    {
        $total = $this->getTotalTasksCount();
        return $total > 0 ? ($this->getCompletedTasksCount() / $total) * 100 : 0;
    }

    public function getCompletedMilestonesCount(): int
    {
        return $this->milestones()->where('status', 'completed')->count();
    }

    public function isOverdue(): bool
    {
        return $this->end_date && $this->end_date->isPast() && $this->status !== 'completed';
    }

    public function getDaysRemaining(): ?int
    {
        return $this->end_date ? now()->diffInDays($this->end_date, false) : null;
    }

    public function getApprovedPhaseCount(): int
    {
        return $this->phases()->where('status', 'approved')->count();
    }

    public function getPhaseProgressPercent(): float
    {
        $total = count(self::PHASES);
        if ($total <= 0) {
            return 0;
        }
        return ($this->getApprovedPhaseCount() / $total) * 100;
    }

    public function getCurrentPhaseNumber(): int
    {
        $approved = $this->getApprovedPhaseCount();
        return min($approved + 1, count(self::PHASES));
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('end_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeBySupervisor($query, $supervisorId)
    {
        return $query->where('supervisor_id', $supervisorId);
    }

    public function scopeByCourse($query, $courseCode)
    {
        return $query->where('course_code', $courseCode);
    }
}
