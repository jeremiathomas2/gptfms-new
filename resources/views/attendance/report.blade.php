<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; margin: 0; }
        .page { padding: 28px 32px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 14px; margin-bottom: 18px; }
        .brand { font-size: 24px; font-weight: 800; color: #2563eb; letter-spacing: 0.04em; }
        .title { font-size: 20px; font-weight: 800; margin-top: 8px; }
        .subtitle { color: #475569; margin-top: 4px; line-height: 1.5; }
        .meta-table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        .meta-table td { width: 50%; vertical-align: top; padding: 4px 0; }
        .meta-label { font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.05em; }
        .meta-value { margin-top: 2px; font-weight: 600; color: #0f172a; }
        .summary { margin: 18px 0 20px; }
        .summary-card { display: inline-block; width: 31.3%; margin-right: 2%; padding: 12px 14px; border: 1px solid #dbeafe; background: #eff6ff; border-radius: 10px; vertical-align: top; }
        .summary-card:last-child { margin-right: 0; }
        .summary-label { font-size: 10px; text-transform: uppercase; color: #475569; font-weight: 700; }
        .summary-value { font-size: 22px; font-weight: 800; color: #1d4ed8; margin-top: 6px; }
        .summary-note { font-size: 11px; color: #475569; margin-top: 4px; }
        .group-card { border: 1px solid #cbd5e1; border-radius: 12px; padding: 16px; margin-bottom: 18px; }
        .group-head { margin-bottom: 12px; }
        .group-title { font-size: 16px; font-weight: 800; color: #0f172a; }
        .group-sub { font-size: 11px; color: #475569; margin-top: 4px; }
        .pill { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .pill.ok { background: #dcfce7; color: #166534; }
        .pill.warn { background: #fef3c7; color: #92400e; }
        .metrics { margin: 12px 0 14px; }
        .metric { display: inline-block; width: 23.5%; margin-right: 2%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 10px; vertical-align: top; }
        .metric:last-child { margin-right: 0; }
        .metric-k { font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: 700; }
        .metric-v { font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 4px; }
        .progress-track { margin-top: 6px; height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden; }
        .progress-fill { height: 8px; background: #2563eb; border-radius: 999px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e2e8f0; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f8fafc; font-size: 10px; text-transform: uppercase; color: #475569; letter-spacing: 0.04em; }
        .cell-muted { color: #64748b; }
        .attendees { margin: 0; padding-left: 14px; }
        .attendees li { margin-bottom: 3px; }
        .empty { padding: 12px; border: 1px dashed #cbd5e1; border-radius: 10px; color: #64748b; }
        .footer { margin-top: 22px; border-top: 1px solid #cbd5e1; padding-top: 10px; font-size: 10px; color: #64748b; text-align: center; }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <div class="brand">{{ $systemTitle }}</div>
            <div class="title">{{ $reportTitle }}</div>
            <div class="subtitle">
                Physical attendance report for supervisor-group meetings. This document includes meeting history,
                participants, progress to the minimum required meetings, and export audit information.
            </div>

            <table class="meta-table">
                <tr>
                    <td>
                        <div class="meta-label">Exported By</div>
                        <div class="meta-value">{{ $exportedBy->name }}</div>
                    </td>
                    <td>
                        <div class="meta-label">Role</div>
                        <div class="meta-value">{{ ucfirst($exportedBy->getRoleNames()->first() ?? 'user') }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="meta-label">Exported Time</div>
                        <div class="meta-value">{{ $exportedAt->format('M d, Y h:i A') }}</div>
                    </td>
                    <td>
                        <div class="meta-label">Minimum Required Meetings</div>
                        <div class="meta-value">{{ (int) $minimumMeetings }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="summary">
            <div class="summary-card">
                <div class="summary-label">Groups In Report</div>
                <div class="summary-value">{{ (int) $totalGroups }}</div>
                <div class="summary-note">Total groups covered in this export</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Meetings Recorded</div>
                <div class="summary-value">{{ (int) $totalMeetings }}</div>
                <div class="summary-note">All physical meetings recorded so far</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Groups Met Minimum</div>
                <div class="summary-value">{{ (int) $groupsMetMinimum }}</div>
                <div class="summary-note">Groups that reached the {{ (int) $minimumMeetings }} meeting target</div>
            </div>
        </div>

        @forelse($groupReports as $groupReport)
            @php
                $group = $groupReport['group'];
                $completed = (int) $groupReport['recorded_meetings'] >= (int) $minimumMeetings;
            @endphp
            <div class="group-card">
                <div class="group-head">
                    <div class="group-title">{{ $group->name }}</div>
                    <div class="group-sub">
                        Project: {{ $group->project?->title ?? 'No project yet' }} |
                        Supervisor: {{ $group->supervisor?->name ?? 'Not assigned' }}
                    </div>
                    <div style="margin-top:8px;">
                        <span class="pill {{ $completed ? 'ok' : 'warn' }}">
                            {{ $completed ? 'Minimum Reached' : 'Below Minimum' }}
                        </span>
                    </div>
                </div>

                <div class="metrics">
                    <div class="metric">
                        <div class="metric-k">Recorded Meetings</div>
                        <div class="metric-v">{{ (int) $groupReport['recorded_meetings'] }}</div>
                    </div>
                    <div class="metric">
                        <div class="metric-k">Group Members</div>
                        <div class="metric-v">{{ (int) $groupReport['member_count'] }}</div>
                    </div>
                    <div class="metric">
                        <div class="metric-k">Progress To Target</div>
                        <div class="metric-v">{{ (int) $groupReport['progress'] }}%</div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ (int) $groupReport['progress'] }}%;"></div>
                        </div>
                    </div>
                    <div class="metric">
                        <div class="metric-k">Required Meetings</div>
                        <div class="metric-v">{{ (int) $minimumMeetings }}</div>
                    </div>
                </div>

                @if(collect($groupReport['records'])->isEmpty())
                    <div class="empty">No physical attendance meetings have been recorded for this group yet.</div>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 8%;">#</th>
                                <th style="width: 12%;">Date</th>
                                <th style="width: 17%;">Meeting</th>
                                <th style="width: 13%;">Location</th>
                                <th style="width: 15%;">Agenda</th>
                                <th style="width: 15%;">Notes</th>
                                <th style="width: 20%;">Attendees</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupReport['records'] as $record)
                                <tr>
                                    <td>{{ (int) $record['meeting_number'] }}</td>
                                    <td>{{ $record['meeting_date'] ? $record['meeting_date']->format('M d, Y') : '—' }}</td>
                                    <td>
                                        <strong>{{ $record['title'] }}</strong><br>
                                        <span class="cell-muted">{{ (int) $record['attendee_count'] }} attended</span>
                                    </td>
                                    <td>{{ $record['location'] ?: '—' }}</td>
                                    <td>{{ $record['agenda'] ?: '—' }}</td>
                                    <td>{{ $record['notes'] ?: '—' }}</td>
                                    <td>
                                        @if(collect($record['attendees'])->isNotEmpty())
                                            <ul class="attendees">
                                                @foreach($record['attendees'] as $attendee)
                                                    <li>
                                                        {{ $attendee['name'] }}
                                                        @if(!empty($attendee['registration_number']))
                                                            <span class="cell-muted">({{ $attendee['registration_number'] }})</span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="cell-muted">No attendees captured</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @empty
            <div class="empty">No groups are available for this attendance report.</div>
        @endforelse

        <div class="footer">
            {{ $systemTitle }} Attendance Reporting Module |
            Export generated on {{ $exportedAt->format('M d, Y h:i A') }} by {{ $exportedBy->name }}
        </div>
    </div>
</body>
</html>
