<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'phone',
        'gender',
        'registration_number',
        'avatar',
        'bio',
        'skills',
        'status',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'last_seen_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'full_name',
        'initials',
        'is_online',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            if (empty($user->name) && !empty($user->first_name)) {
                $user->name = $user->first_name . ($user->last_name ? ' ' . $user->last_name : '');
            }
            if (empty($user->registration_number)) {
                $user->registration_number = 'REG-' . strtoupper(\Str::random(8));
            }
        });
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getInitialsAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
        }
        
        $names = explode(' ', $this->name);
        if (count($names) >= 2) {
            return strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1));
        }
        
        return strtoupper(substr($this->name, 0, 2));
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->getRoleNames()->first(),
            'permissions' => $this->getAllPermissions()->pluck('name'),
        ];
    }

    public function getIsOnlineAttribute(): bool
    {
        if (!$this->last_seen_at) {
            return false;
        }
        
        return $this->last_seen_at->gt(now()->subMinutes(5));
    }

    // Relationships
    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function supervisorProfile(): HasOne
    {
        return $this->hasOne(SupervisorProfile::class);
    }

    public function studentSkillsSurvey(): HasOne
    {
        return $this->hasOne(StudentSkillsSurvey::class);
    }

    public function createdGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Alias for groupMemberships to support FinalProject naming conventions
     */
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    public function activeGroup(): HasOne
    {
        return $this->hasOne(GroupMember::class)
            ->where('status', 'joined')
            ->with('group');
    }

    public function supervisedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'supervisor_id');
    }

    public function supervisedGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'supervisor_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function peerEvaluationsGiven(): HasMany
    {
        return $this->hasMany(PeerEvaluation::class, 'evaluator_id');
    }

    public function peerEvaluationsReceived(): HasMany
    {
        return $this->hasMany(PeerEvaluation::class, 'evaluated_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeStudents($query)
    {
        return $query->role('student');
    }

    public function scopeSupervisors($query)
    {
        return $query->role('supervisor');
    }

    public function scopeAdmins($query)
    {
        return $query->role('admin');
    }
}
