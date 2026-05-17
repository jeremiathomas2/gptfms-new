<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupervisorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department',
        'position',
        'bio',
        'specializations',
        'years_of_experience',
        'highest_education',
        'certifications',
        'preferences',
        'max_students',
        'is_available',
        'last_activity_at',
    ];

    protected $casts = [
        'specializations' => 'array',
        'preferences' => 'array',
        'is_available' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Get the user that owns the supervisor profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get supervised students through projects or assignments.
     */
    public function supervisedStudents(): HasMany
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    /**
     * Get supervisor by user ID.
     */
    public static function getByUserId(int $userId): ?self
    {
        return static::where('user_id', $userId)->first();
    }

    /**
     * Get available supervisors.
     */
    public static function getAvailable(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_available', true)
            ->with('user')
            ->get();
    }

    /**
     * Get supervisors by department.
     */
    public static function getByDepartment(string $department): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('department', $department)
            ->with('user')
            ->get();
    }

    /**
     * Get supervisors by specialization.
     */
    public static function getBySpecialization(string $specialization): \Illuminate\Database\Eloquent\Collection
    {
        return static::whereJsonContains('specializations', $specialization)
            ->with('user')
            ->get();
    }

    /**
     * Update last activity timestamp.
     */
    public function updateLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Check if supervisor can accept more students.
     */
    public function canAcceptMoreStudents(): bool
    {
        return $this->is_available && $this->supervisedStudents()->count() < $this->max_students;
    }

    /**
     * Get current student count.
     */
    public function getCurrentStudentCount(): int
    {
        return $this->supervisedStudents()->count();
    }

    /**
     * Get availability status as text.
     */
    public function getAvailabilityStatus(): string
    {
        if (!$this->is_available) {
            return 'Not Available';
        }
        
        $currentCount = $this->getCurrentStudentCount();
        $maxStudents = $this->max_students;
        
        if ($currentCount >= $maxStudents) {
            return 'Full Capacity';
        } elseif ($currentCount >= $maxStudents * 0.8) {
            return 'Limited Availability';
        } else {
            return 'Available';
        }
    }
}
