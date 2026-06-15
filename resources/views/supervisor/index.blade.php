@extends('layouts.app')

@section('breadcrumb', 'Supervisor')

@section('content')
<div class="page active" id="page-supervisor">
    <div class="section-header">
        <div><div class="section-title">Supervisor Hub</div><div class="section-sub">Review phase submissions, assign tasks, and track progress across your groups</div></div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a class="btn btn-outline btn-sm" href="{{ route('attendance') }}"><i class="uil uil-calendar-alt me-1"></i> Attendance</a>
            <a class="btn btn-outline btn-sm" href="{{ route('projects') }}"><i class="uil uil-folder me-1"></i> Projects</a>
            <a class="btn btn-primary btn-sm" href="{{ route('tasks') }}"><i class="uil uil-check-circle me-1"></i> Tasks</a>
        </div>
    </div>
    <div class="grid-4">
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">My Groups</div><div class="stat-value">{{ (int) ($myGroups?->count() ?? 0) }}</div><div class="stat-change up"><i class="uil uil-users-alt"></i> Active supervision</div></div><div class="stat-icon si-blue"><i class="uil uil-users-alt"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Pending Phase Reviews</div><div class="stat-value">{{ (int) ($pendingPhaseCount ?? 0) }}</div><div class="stat-change {{ ((int) ($pendingPhaseCount ?? 0)) > 0 ? 'down' : 'up' }}"><i class="uil uil-clipboard-notes"></i> Awaiting approval</div></div><div class="stat-icon si-amber"><i class="uil uil-clipboard-notes"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Tasks In Review</div><div class="stat-value">{{ (int) ($reviewTaskCount ?? 0) }}</div><div class="stat-change up"><i class="uil uil-spinner"></i> Student submissions</div></div><div class="stat-icon si-green"><i class="uil uil-spinner"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Overdue Tasks</div><div class="stat-value">{{ (int) ($overdueTaskCount ?? 0) }}</div><div class="stat-change {{ ((int) ($overdueTaskCount ?? 0)) > 0 ? 'down' : 'up' }}"><i class="uil uil-exclamation-triangle"></i> Needs action</div></div><div class="stat-icon si-red"><i class="uil uil-exclamation-triangle"></i></div></div></div>
    </div>

    <div class="grid-7030">
        <div class="card" style="padding:18px">
            <div class="section-header" style="margin-bottom:12px">
                <div class="section-title" style="font-size:15px">Latest Phase Submissions</div>
                <span class="badge badge-blue"><i class="uil uil-chart-line me-1"></i> Avg progress: {{ (int) ($avgProgress ?? 0) }}%</span>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Project</th>
                        <th>Group</th>
                        <th>Phase</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse(($latestPhaseSubmissions ?? collect()) as $p)
                        @php
                            $badge = $p->status === 'submitted' ? 'badge-blue' : ($p->status === 'changes_requested' ? 'badge-amber' : 'badge-gray');
                        @endphp
                        <tr>
                            <td><strong>{{ $p->project?->title ?? 'Project' }}</strong></td>
                            <td>{{ $p->project?->group?->name ?? 'Group' }}</td>
                            <td>Phase {{ (int) $p->phase_number }} — {{ $p->phase_title }}</td>
                            <td><span class="badge {{ $badge }}">{{ str_replace('_', ' ', ucfirst($p->status)) }}</span></td>
                            <td>{{ $p->submitted_at ? $p->submitted_at->diffForHumans() : '—' }}</td>
                            <td><a class="btn btn-primary btn-sm" href="{{ route('projects.show', $p->project) }}#phase-card-{{ (int) $p->phase_number }}"><i class="uil uil-eye me-1"></i> Review</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="color:var(--text-muted)">No submissions yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card" style="padding:18px">
            <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-users-alt me-2"></i> My Groups</div>
            <div style="display:grid;gap:10px">
                @forelse(($myGroups ?? collect()) as $g)
                    <div class="task-card">
                        <div class="task-title">{{ $g->name }}</div>
                        <div class="task-meta" style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap">
                            <div style="color:var(--text-muted)">
                                {{ $g->project?->title ?? 'No project yet' }}
                            </div>
                            <div style="display:flex;gap:8px;align-items:center">
                                <span class="badge badge-gray"><i class="uil uil-user me-1"></i> {{ (int) ($g->active_members_count ?? 0) }}</span>
                                @if($g->project)
                                    <span class="badge badge-blue">{{ (int) round((float) $g->project->progress_percentage) }}%</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="color:var(--text-muted)">No groups assigned yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
