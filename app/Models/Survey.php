<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Survey extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'name',
        'skills_data',
        'completed'
    ];
    
    protected $casts = [
        'skills_data' => 'array',
        'completed' => 'boolean'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Check if user has completed survey
     */
    public static function isCompletedByUser($userId)
    {
        return self::where('user_id', $userId)->where('completed', true)->exists();
    }
    
    /**
     * Get or create survey for user
     */
    public static function getOrCreateForUser($userId, $userName = null)
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'name' => $userName ?? 'Unknown',
                'skills_data' => [],
                'completed' => false
            ]
        );
    }
}
