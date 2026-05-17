<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupSettings extends Model
{
    protected $fillable = [
        'participants_per_group',
        'countdown_minutes',
        'balance_by_gender',
        'balance_by_skills',
        'auto_create_groups',
        'is_active',
        'countdown_start_time',
        'countdown_end_time',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'balance_by_gender' => 'boolean',
        'balance_by_skills' => 'boolean',
        'auto_create_groups' => 'boolean',
        'is_active' => 'boolean',
        'countdown_start_time' => 'datetime',
        'countdown_end_time' => 'datetime',
    ];

    /**
     * Check if countdown is currently running
     */
    public function isCountdownRunning()
    {
        return $this->is_active && 
               $this->countdown_end_time && 
               $this->countdown_end_time->isFuture();
    }

    /**
     * Check if countdown is currently running (snake_case alias)
     */
    public function is_countdown_running()
    {
        return $this->isCountdownRunning();
    }

    /**
     * Get remaining time in seconds
     */
    public function getRemainingTime()
    {
        if (!$this->isCountdownRunning()) {
            return 0;
        }

        return max(0, $this->countdown_end_time->diffInSeconds(now()));
    }

    /**
     * Get formatted remaining time
     */
    public function getFormattedRemainingTimeAttribute()
    {
        $remaining = $this->getRemainingTime();
        
        if ($remaining <= 0) {
            return '00:00:00';
        }

        $hours = floor($remaining / 3600);
        $minutes = floor(($remaining % 3600) / 60);
        $seconds = $remaining % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Start countdown
     */
    public function startCountdown()
    {
        $this->update([
            'is_active' => true,
            'countdown_start_time' => now(),
            'countdown_end_time' => now()->addMinutes($this->countdown_minutes),
        ]);
    }

    /**
     * Stop countdown
     */
    public function stopCountdown()
    {
        $this->update([
            'is_active' => false,
            'countdown_start_time' => null,
            'countdown_end_time' => null,
        ]);
    }

    /**
     * Get current settings or create default
     */
    public static function getCurrent()
    {
        return self::first() ?? self::createDefault();
    }

    /**
     * Create default settings
     */
    public static function createDefault()
    {
        return self::create([
            'participants_per_group' => 4,
            'countdown_minutes' => 60,
            'balance_by_gender' => true,
            'balance_by_skills' => true,
            'auto_create_groups' => true,
            'is_active' => false,
        ]);
    }
}
