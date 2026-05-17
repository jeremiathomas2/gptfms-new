<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'gpa',
        'major',
        'semester',
        'skills',
        'availability',
        'personality_traits',
        'bio',
        'preferred_group_size',
        'preferred_roles',
        'total_projects',
        'average_rating',
    ];

    protected $casts = [
        'skills' => 'array',
        'availability' => 'array',
        'personality_traits' => 'array',
        'preferred_roles' => 'array',
        'gpa' => 'decimal:2',
        'average_rating' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'user_id', 'user_id');
    }

    public function peerEvaluationsGiven(): HasMany
    {
        return $this->hasMany(PeerEvaluation::class, 'evaluator_id', 'user_id');
    }

    public function peerEvaluationsReceived(): HasMany
    {
        return $this->hasMany(PeerEvaluation::class, 'evaluated_id', 'user_id');
    }

    // Helper methods for skills
    public function getSkillNames(): array
    {
        return collect($this->skills)->pluck('name')->toArray();
    }

    public function getProficiencyLevel(string $skillName): ?int
    {
        $skill = collect($this->skills)->firstWhere('name', $skillName);
        return $skill ? $skill['proficiency'] : null;
    }

    public function hasSkill(string $skillName): bool
    {
        return collect($this->skills)->pluck('name')->contains($skillName);
    }

    // Scopes
    public function scopeByMajor($query, string $major)
    {
        return $query->where('major', $major);
    }

    public function scopeByGpaRange($query, float $min, float $max = 4.0)
    {
        return $query->whereBetween('gpa', [$min, $max]);
    }

    public function scopeBySemester($query, int $semester)
    {
        return $query->where('semester', $semester);
    }
}
