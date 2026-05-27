<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPhase extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'phase_number',
        'phase_title',
        'submission',
        'submitted_by',
        'submitted_at',
        'status',
        'supervisor_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'phase_number' => 'integer',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

