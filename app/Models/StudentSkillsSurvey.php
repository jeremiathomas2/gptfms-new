<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSkillsSurvey extends Model
{
    use HasFactory;
    
    protected $table = 'student_skills_survey';

    protected $fillable = [
        'user_id',
        'skills',
        'experience_level',
        'interests',
        'project_type',
        'project_duration',
        'goals',
        'completed_at',
    ];

    protected $casts = [
        'skills' => 'array',
        'interests' => 'array',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the survey.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a user has completed the survey
     */
    public static function isCompletedByUser(int $userId): bool
    {
        return static::where('user_id', $userId)
                    ->whereNotNull('completed_at')
                    ->exists();
    }

    /**
     * Get survey by user
     */
    public static function getByUser(int $userId): ?self
    {
        return static::where('user_id', $userId)->first();
    }

    /**
     * Get all surveys by experience level
     */
    public static function getByExperienceLevel(string $level): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('experience_level', $level)->with('user')->get();
    }

    /**
     * Get users with specific skills
     */
    public static function getUsersWithSkill(string $skill): \Illuminate\Database\Eloquent\Collection
    {
        return static::whereJsonContains('skills', $skill)->with('user')->get();
    }

    /**
     * Get users with specific interests
     */
    public static function getUsersWithInterest(string $interest): \Illuminate\Database\Eloquent\Collection
    {
        return static::whereJsonContains('interests', $interest)->with('user')->get();
    }
}
