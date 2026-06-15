@extends('layouts.app')

@section('breadcrumb', 'Attendance')

@section('content')
@php
    $mode = $mode ?? 'student';
    $minimumMeetings = (int) ($minimumMeetings ?? 5);
    $groups = $groups ?? collect();
@endphp
<div class="page active" id="page-attendance">
    <div class="section-header">
        <div>
            <div class="section-title">Physical Meeting Attendance</div>
            <div class="section-sub">
                @if($mode === 'student')
                    View your group's physical meetings with the supervisor. Minimum required meetings: {{ $minimumMeetings }}.
                @else
                    Record and review physical group meetings with supervisors. Minimum required meetings per group: {{ $minimumMeetings }}.
                @endif
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a class="btn btn-primary btn-sm" href="{{ route('attendance.report.download') }}"><i class="uil uil-file-download-alt me-1"></i> Download PDF Report</a>
            <a class="btn btn-outline btn-sm" href="{{ route('reports') }}"><i class="uil uil-analytics me-1"></i> Analytics</a>
            <a class="btn btn-outline btn-sm" href="{{ route('projects') }}"><i class="uil uil-folder me-1"></i> Projects</a>
        </div>
    </div>

    @if(session('success'))
        <div class="card" style="padding:12px 14px;margin-bottom:16px;border:1px solid rgba(16,185,129,.25);background:rgba(16,185,129,.08);color:var(--text)">
            <i class="uil uil-check-circle me-1" style="color:var(--success)"></i> {{ session('success') }}
        </div>
    @endif

    @if($mode === 'student')
        @php
            $meetingCount = (int) ($group?->attendanceRecords?->count() ?? 0);
            $meetingProgress = $minimumMeetings > 0 ? min(100, (int) round(($meetingCount / $minimumMeetings) * 100)) : 0;
            $memberCount = (int) ($group?->activeMembers?->count() ?? 0);
        @endphp

        @if(!$group)
            <div class="card" style="padding:24px;text-align:center;color:var(--text-muted)">
                <i class="uil uil-users-alt" style="font-size:34px;display:block;margin-bottom:8px"></i>
                No group assigned yet, so there are no attendance meetings to show.
            </div>
        @else
            <div class="grid-4" style="margin-bottom:18px">
                <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">My Group</div><div class="stat-value">{{ $group->name }}</div></div><div class="stat-icon si-blue"><i class="uil uil-users-alt"></i></div></div></div>
                <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Meetings Held</div><div class="stat-value">{{ $meetingCount }}</div></div><div class="stat-icon si-green"><i class="uil uil-calendar-alt"></i></div></div></div>
                <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Minimum Target</div><div class="stat-value">{{ $minimumMeetings }}</div></div><div class="stat-icon si-amber"><i class="uil uil-check-circle"></i></div></div></div>
                <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Progress To Target</div><div class="stat-value">{{ $meetingProgress }}%</div></div><div class="stat-icon si-red"><i class="uil uil-chart-line"></i></div></div></div>
            </div>

            <div class="grid-7030">
                <div class="card" style="padding:18px">
                    <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-calendar-alt me-2"></i> Meeting History</div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th>Meeting</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Attended</th>
                                <th>Notes</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($group->attendanceRecords as $record)
                                <tr>
                                    <td><strong>Meeting {{ (int) $record->meeting_number }}</strong><div style="color:var(--text-muted);font-size:12px">{{ $record->title }}</div></td>
                                    <td>{{ $record->meeting_date ? $record->meeting_date->format('M d, Y') : '—' }}</td>
                                    <td>{{ $record->location ?: '—' }}</td>
                                    <td>{{ (int) $record->attendee_count }}/{{ $memberCount }}</td>
                                    <td style="max-width:280px;color:var(--text-muted)">{{ $record->notes ?: ($record->agenda ?: '—') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" style="color:var(--text-muted)">No physical meetings have been recorded yet.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card" style="padding:18px">
                    <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-user-md me-2"></i> Supervisor</div>
                    <div style="display:grid;gap:8px">
                        <div><strong>{{ $group->supervisor?->name ?? 'Not assigned' }}</strong></div>
                        <div style="color:var(--text-muted)">{{ $group->project?->title ?? 'No project yet' }}</div>
                        <div class="progress-bar" style="margin-top:10px">
                            <div class="progress-fill" style="width:{{ $meetingProgress }}%"></div>
                        </div>
                        <div style="font-size:12px;color:var(--text-muted)">
                            {{ $meetingCount }} of {{ $minimumMeetings }} physical meetings completed.
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        @php
            $totalMeetings = (int) $groups->sum(fn ($g) => (int) ($g->attendance_records_count ?? $g->attendanceRecords->count()));
            $groupsMetMinimum = (int) $groups->filter(fn ($g) => ((int) ($g->attendance_records_count ?? $g->attendanceRecords->count())) >= $minimumMeetings)->count();
            $avgProgress = $groups->count() > 0
                ? (int) round($groups->avg(fn ($g) => min(100, (((int) ($g->attendance_records_count ?? $g->attendanceRecords->count())) / $minimumMeetings) * 100)))
                : 0;
        @endphp

        <div class="grid-4" style="margin-bottom:18px">
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Groups</div><div class="stat-value">{{ (int) $groups->count() }}</div></div><div class="stat-icon si-blue"><i class="uil uil-users-alt"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Meetings Recorded</div><div class="stat-value">{{ $totalMeetings }}</div></div><div class="stat-icon si-green"><i class="uil uil-calendar-alt"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Groups Met Minimum</div><div class="stat-value">{{ $groupsMetMinimum }}</div></div><div class="stat-icon si-amber"><i class="uil uil-check-circle"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Avg. Attendance Progress</div><div class="stat-value">{{ $avgProgress }}%</div></div><div class="stat-icon si-red"><i class="uil uil-chart-line"></i></div></div></div>
        </div>

        <div class="grid-7030">
            <div class="card" style="padding:18px">
                <div class="section-title" style="font-size:15px;margin-bottom:16px"><i class="uil uil-plus-circle me-2"></i> Record Physical Meeting</div>
                @if($groups->isEmpty())
                    <div style="color:var(--text-muted)">No groups are available for attendance recording.</div>
                @else
                    <form method="POST" action="{{ route('attendance.store') }}">
                        @csrf
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">Group</label>
                                <select class="form-control" name="group_id" id="attendance-group-select" required onchange="window.updateAttendanceMembers && window.updateAttendanceMembers(this)">
                                    <option value="">Select group</option>
                                    @foreach($groups as $g)
                                        <option value="{{ $g->id }}" data-members='@json($g->activeMembers->map(fn ($member) => ["id" => $member->user_id, "name" => $member->user?->name, "role" => $member->role])->values())'>
                                            {{ $g->name }} @if($g->project) - {{ $g->project->title }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Meeting Date</label>
                                <input class="form-control" type="date" name="meeting_date" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">Meeting Title</label>
                                <input class="form-control" name="title" placeholder="Weekly physical supervision meeting" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Location</label>
                                <input class="form-control" name="location" placeholder="Supervisor office / Lab 2">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Agenda</label>
                            <textarea class="form-control" name="agenda" rows="3" placeholder="Discuss project progress, blockers, and next actions"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Present Students</label>
                            <div id="attendance-members-box" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;padding:12px;border:1px solid var(--border);border-radius:12px;background:var(--bg-alt);min-height:58px">
                                <div style="color:var(--text-muted)">Select a group to mark attendees.</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Important outcomes, concerns, or physical attendance notes"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="uil uil-save me-1"></i> Save Attendance</button>
                    </form>
                @endif
            </div>
            <div class="card" style="padding:18px">
                <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-check-circle me-2"></i> Minimum Requirement</div>
                <div style="color:var(--text-muted);line-height:1.65">
                    Each group should attend at least <strong>{{ $minimumMeetings }}</strong> physical meetings with its supervisor.
                </div>
                <div style="margin-top:14px;display:grid;gap:10px">
                    @forelse($groups as $g)
                        @php
                            $count = (int) ($g->attendance_records_count ?? $g->attendanceRecords->count());
                            $progress = min(100, (int) round(($count / $minimumMeetings) * 100));
                        @endphp
                        <div>
                            <div style="display:flex;justify-content:space-between;gap:10px;margin-bottom:6px">
                                <strong>{{ $g->name }}</strong>
                                <span class="badge {{ $count >= $minimumMeetings ? 'badge-green' : 'badge-amber' }}">{{ $count }}/{{ $minimumMeetings }}</span>
                            </div>
                            <div class="progress-bar"><div class="progress-fill" style="width:{{ $progress }}%"></div></div>
                        </div>
                    @empty
                        <div style="color:var(--text-muted)">No groups assigned yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card" style="padding:18px;margin-top:18px">
            <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-calendar-alt me-2"></i> Attendance Records</div>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Group</th>
                        <th>Meeting</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Attended</th>
                        <th>Summary</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($groups as $g)
                        @forelse($g->attendanceRecords as $record)
                            <tr>
                                <td><strong>{{ $g->name }}</strong><div style="color:var(--text-muted);font-size:12px">{{ $g->project?->title ?? 'No project yet' }}</div></td>
                                <td>Meeting {{ (int) $record->meeting_number }}<div style="color:var(--text-muted);font-size:12px">{{ $record->title }}</div></td>
                                <td>{{ $record->meeting_date ? $record->meeting_date->format('M d, Y') : '—' }}</td>
                                <td>{{ $record->location ?: '—' }}</td>
                                <td>{{ (int) $record->attendee_count }}/{{ (int) $g->activeMembers->count() }}</td>
                                <td style="max-width:320px;color:var(--text-muted)">{{ $record->notes ?: ($record->agenda ?: '—') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td><strong>{{ $g->name }}</strong></td>
                                <td colspan="5" style="color:var(--text-muted)">No attendance recorded yet.</td>
                            </tr>
                        @endforelse
                    @empty
                        <tr><td colspan="6" style="color:var(--text-muted)">No attendance records available.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    window.updateAttendanceMembers = function(select) {
        const box = document.getElementById('attendance-members-box');
        if (!box) return;

        const option = select.options[select.selectedIndex];
        let members = [];
        try {
            members = JSON.parse(option?.getAttribute('data-members') || '[]');
        } catch (e) {
            members = [];
        }

        if (!members.length) {
            box.innerHTML = '<div style="color:var(--text-muted)">Select a group to mark attendees.</div>';
            return;
        }

        box.innerHTML = members.map((member) => `
            <label style="display:flex;align-items:center;gap:10px;padding:10px;border:1px solid var(--border);border-radius:10px;background:var(--card);cursor:pointer">
                <input type="checkbox" name="attendee_ids[]" value="${member.id}" checked>
                <span>
                    <strong>${member.name || 'Member'}</strong>
                    <span style="display:block;color:var(--text-muted);font-size:12px;text-transform:capitalize">${member.role || 'member'}</span>
                </span>
            </label>
        `).join('');
    };
</script>
@endpush
