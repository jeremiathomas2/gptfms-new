<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupAttendance extends Model
{
    use HasFactory;

    public const MINIMUM_REQUIRED_MEETINGS = 5;

    protected $fillable = [
        'group_id',
        'supervisor_id',
        'meeting_number',
        'meeting_date',
        'location',
        'title',
        'agenda',
        'attendee_ids',
        'attendee_count',
        'notes',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'attendee_ids' => 'array',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
