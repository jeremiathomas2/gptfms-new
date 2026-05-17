<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeerEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluator_id',
        'evaluated_id',
        'project_id',
        'contribution_score',
        'teamwork_score',
        'communication_score',
        'quality_score',
        'timeliness_score',
        'comments',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'overall_score' => 'decimal:2',
    ];

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function evaluated(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Scopes
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByEvaluator($query, $evaluatorId)
    {
        return $query->where('evaluator_id', $evaluatorId);
    }

    public function scopeByEvaluated($query, $evaluatedId)
    {
        return $query->where('evaluated_id', $evaluatedId);
    }
}
