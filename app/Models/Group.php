<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'max_members',
        'project_id',
        'created_by',
        'supervisor_id',
        'formation_criteria',
        'formation_score',
        'formed_at',
        'archived_at',
    ];

    protected $casts = [
        'formation_criteria' => 'array',
        'formation_score' => 'decimal:2',
        'formed_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class)->where('status', 'joined');
    }

    public function leader(): HasMany
    {
        return $this->hasMany(GroupMember::class)->where('role', 'leader');
    }

    public function tasks(): HasMany
    {
        return $this->hasManyThrough(Task::class, Project::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // Helper methods
    public function getCurrentMemberCount(): int
    {
        return $this->activeMembers()->count();
    }

    public function isFull(): bool
    {
        return $this->getCurrentMemberCount() >= $this->max_members;
    }

    public function getSkillDistribution(): array
    {
        $skills = [];
        foreach ($this->activeMembers as $member) {
            if ($member->user->studentProfile) {
                foreach ($member->user->studentProfile->skills ?? [] as $skill) {
                    $skillName = $skill['name'];
                    if (!isset($skills[$skillName])) {
                        $skills[$skillName] = ['count' => 0, 'total_proficiency' => 0];
                    }
                    $skills[$skillName]['count']++;
                    $skills[$skillName]['total_proficiency'] += $skill['proficiency'] ?? 0;
                }
            }
        }

        foreach ($skills as $name => &$data) {
            $data['average_proficiency'] = $data['total_proficiency'] / $data['count'];
            unset($data['total_proficiency']);
        }

        return $skills;
    }

    public function getAverageGpa(): ?float
    {
        $gpas = $this->activeMembers()
            ->whereHas('user.studentProfile')
            ->with('user.studentProfile')
            ->get()
            ->pluck('user.studentProfile.gpa')
            ->filter();

        return $gpas->isNotEmpty() ? $gpas->avg() : null;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForming($query)
    {
        return $query->where('status', 'forming');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'forming')
            ->whereRaw('(SELECT COUNT(*) FROM group_members WHERE group_id = groups.id AND status = "joined") < max_members');
    }
}
