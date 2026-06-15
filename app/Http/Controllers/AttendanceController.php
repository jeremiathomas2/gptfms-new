<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupAttendance;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $minimumMeetings = GroupAttendance::MINIMUM_REQUIRED_MEETINGS;

        if ($user->hasRole('supervisor')) {
            $groups = Group::query()
                ->where('supervisor_id', $user->id)
                ->with([
                    'project',
                    'attendanceRecords',
                    'activeMembers.user',
                ])
                ->withCount('attendanceRecords')
                ->orderBy('name')
                ->get();

            return view('attendance.index', [
                'mode' => 'supervisor',
                'groups' => $groups,
                'minimumMeetings' => $minimumMeetings,
            ]);
        }

        if ($user->hasRole('admin')) {
            $groups = Group::query()
                ->with([
                    'project',
                    'supervisor',
                    'attendanceRecords',
                    'activeMembers.user',
                ])
                ->withCount('attendanceRecords')
                ->orderBy('name')
                ->get();

            return view('attendance.index', [
                'mode' => 'admin',
                'groups' => $groups,
                'minimumMeetings' => $minimumMeetings,
            ]);
        }

        $membership = GroupMember::query()
            ->where('user_id', $user->id)
            ->where('status', 'joined')
            ->with([
                'group.project',
                'group.supervisor',
                'group.activeMembers.user',
                'group.attendanceRecords',
            ])
            ->first();

        $group = $membership?->group;

        return view('attendance.index', [
            'mode' => 'student',
            'group' => $group,
            'minimumMeetings' => $minimumMeetings,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && ($user->hasRole('supervisor') || $user->hasRole('admin')), 403);

        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'meeting_date' => 'required|date',
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'agenda' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:5000',
            'attendee_ids' => 'nullable|array',
            'attendee_ids.*' => 'integer|exists:users,id',
        ]);

        $group = Group::query()
            ->with(['activeMembers'])
            ->findOrFail($validated['group_id']);

        if ($user->hasRole('supervisor') && (int) $group->supervisor_id !== (int) $user->id) {
            abort(403);
        }

        $activeMemberIds = $group->activeMembers
            ->pluck('user_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values();

        $attendeeIds = collect($validated['attendee_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->intersect($activeMemberIds)
            ->values()
            ->all();

        $meetingNumber = ((int) $group->attendanceRecords()->max('meeting_number')) + 1;

        GroupAttendance::create([
            'group_id' => $group->id,
            'supervisor_id' => (int) ($group->supervisor_id ?: $user->id),
            'meeting_number' => $meetingNumber,
            'meeting_date' => $validated['meeting_date'],
            'title' => $validated['title'],
            'location' => $validated['location'] ?? null,
            'agenda' => $validated['agenda'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'attendee_ids' => $attendeeIds,
            'attendee_count' => count($attendeeIds),
        ]);

        return redirect()
            ->route('attendance')
            ->with('success', 'Physical meeting attendance recorded successfully.');
    }

    public function downloadReport()
    {
        $user = Auth::user();
        $minimumMeetings = GroupAttendance::MINIMUM_REQUIRED_MEETINGS;
        $exportedAt = now();

        if ($user->hasRole('supervisor')) {
            $groups = Group::query()
                ->where('supervisor_id', $user->id)
                ->with([
                    'project',
                    'supervisor',
                    'activeMembers.user',
                    'attendanceRecords',
                ])
                ->orderBy('name')
                ->get();

            $filename = 'attendance-report-supervisor-' . $exportedAt->format('Y-m-d-His') . '.pdf';
            $scopeLabel = 'Supervisor Attendance Report';
        } elseif ($user->hasRole('admin')) {
            $groups = Group::query()
                ->with([
                    'project',
                    'supervisor',
                    'activeMembers.user',
                    'attendanceRecords',
                ])
                ->orderBy('name')
                ->get();

            $filename = 'attendance-report-admin-' . $exportedAt->format('Y-m-d-His') . '.pdf';
            $scopeLabel = 'System Attendance Report';
        } else {
            $membership = GroupMember::query()
                ->where('user_id', $user->id)
                ->where('status', 'joined')
                ->with([
                    'group.project',
                    'group.supervisor',
                    'group.activeMembers.user',
                    'group.attendanceRecords',
                ])
                ->first();

            $groups = collect($membership?->group ? [$membership->group] : []);
            $filename = 'attendance-report-student-' . $exportedAt->format('Y-m-d-His') . '.pdf';
            $scopeLabel = 'Student Attendance Report';
        }

        $groupReports = $groups->map(function ($group) use ($minimumMeetings) {
            $memberMap = $group->activeMembers
                ->filter(fn ($member) => $member->user)
                ->mapWithKeys(function ($member) {
                    return [
                        (int) $member->user_id => [
                            'name' => $member->user->name,
                            'registration_number' => $member->user->registration_number,
                            'role' => $member->role,
                        ],
                    ];
                });

            $memberCount = (int) $group->activeMembers->count();
            $recordedMeetings = (int) $group->attendanceRecords->count();
            $progress = $minimumMeetings > 0
                ? min(100, (int) round(($recordedMeetings / $minimumMeetings) * 100))
                : 0;

            $records = $group->attendanceRecords->map(function ($record) use ($memberMap) {
                $attendeeIds = collect($record->attendee_ids ?? [])
                    ->map(fn ($id) => (int) $id)
                    ->values();

                $attendees = $attendeeIds
                    ->map(fn ($id) => $memberMap[$id] ?? null)
                    ->filter()
                    ->values();

                return [
                    'meeting_number' => (int) $record->meeting_number,
                    'meeting_date' => $record->meeting_date,
                    'title' => $record->title,
                    'location' => $record->location,
                    'agenda' => $record->agenda,
                    'notes' => $record->notes,
                    'attendee_count' => (int) $record->attendee_count,
                    'attendees' => $attendees,
                ];
            });

            return [
                'group' => $group,
                'member_count' => $memberCount,
                'recorded_meetings' => $recordedMeetings,
                'progress' => $progress,
                'records' => $records,
            ];
        });

        $pdf = app('dompdf.wrapper');
        $pdf->setPaper('a4', 'portrait');
        $pdf->loadView('attendance.report', [
            'systemTitle' => 'GPTFMS',
            'reportTitle' => $scopeLabel,
            'minimumMeetings' => $minimumMeetings,
            'exportedAt' => $exportedAt,
            'exportedBy' => $user,
            'groupReports' => $groupReports,
            'totalGroups' => $groupReports->count(),
            'totalMeetings' => (int) $groupReports->sum('recorded_meetings'),
            'groupsMetMinimum' => (int) $groupReports->filter(fn ($groupReport) => (int) $groupReport['recorded_meetings'] >= $minimumMeetings)->count(),
        ]);

        return $pdf->download($filename);
    }
}
