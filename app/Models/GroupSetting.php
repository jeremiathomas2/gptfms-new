<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'participants_per_group',
        'countdown_minutes',
        'countdown_end_time',
        'is_active',
        'auto_create_groups',
        'balance_by_gender',
        'balance_by_skills',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'countdown_end_time' => 'datetime',
        'is_active' => 'boolean',
        'auto_create_groups' => 'boolean',
        'balance_by_gender' => 'boolean',
        'balance_by_skills' => 'boolean',
    ];

    /**
     * Get the user who created the settings
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the settings
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get remaining time in seconds
     */
    public function getRemainingTimeAttribute()
    {
        if (!$this->countdown_end_time || !$this->is_active) {
            return 0;
        }

        $now = now();
        if ($this->countdown_end_time->isPast()) {
            return 0;
        }

        return $this->countdown_end_time->diffInSeconds($now);
    }

    /**
     * Check if countdown is still running
     */
    public function isCountdownRunning()
    {
        return $this->is_active && $this->countdown_end_time && $this->countdown_end_time->isFuture();
    }

    /**
     * Get formatted remaining time
     */
    public function getFormattedRemainingTimeAttribute()
    {
        $seconds = $this->remaining_time;
        
        if ($seconds <= 0) {
            return '00:00:00';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
