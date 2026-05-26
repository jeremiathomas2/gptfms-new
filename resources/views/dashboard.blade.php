@extends('layouts.app')

@section('breadcrumb', 'Dashboard')

@section('content')
@php
    $userName = auth()->user() ? auth()->user()->name : 'Guest User';
    $firstName = explode(' ', $userName)[0] ?? $userName;
@endphp
<div class="page active" id="page-dashboard">
    <div class="section-header">
        <div>
            <div class="section-title">
                <span id="greeting-text">Good morning</span>, {{ $firstName }}
                <span id="greeting-icon"><i class="uil uil-sun" style="color: #f59e0b;"></i></span>
            </div>
            <div class="section-sub">
                @if(($dashboardRole ?? 'student') === 'admin')
                    Monitor activity, users, groups, and system operations.
                @elseif(($dashboardRole ?? 'student') === 'supervisor')
                    Manage supervision workload, deadlines, and reviews.
                @else
                    Track your group, tasks, messages, and evaluations.
                @endif
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a class="btn btn-outline btn-sm" href="{{ route('messages') }}"><i class="uil uil-comment-dots me-1"></i> Messages <span class="badge badge-blue" style="margin-left:6px">{{ (int) ($unreadMessages ?? 0) }}</span></a>
            @if(($dashboardRole ?? 'student') === 'admin')
                <a class="btn btn-outline btn-sm" href="{{ route('admin') }}"><i class="uil uil-wrench me-1"></i> Admin Control</a>
                <a class="btn btn-primary btn-sm" href="{{ route('users') }}"><i class="uil uil-user-circle me-1"></i> User Management</a>
            @elseif(($dashboardRole ?? 'student') === 'supervisor')
                <a class="btn btn-primary btn-sm" href="{{ route('my_group') }}"><i class="uil uil-users-alt me-1"></i> My Groups</a>
                <a class="btn btn-outline btn-sm" href="{{ route('evaluation') }}"><i class="uil uil-star me-1"></i> Evaluations</a>
            @else
                <a class="btn btn-primary btn-sm" href="{{ route('my_group') }}"><i class="uil uil-users-alt me-1"></i> My Group</a>
                <a class="btn btn-outline btn-sm" href="{{ route('evaluation') }}"><i class="uil uil-star me-1"></i> Peer Eval</a>
            @endif
        </div>
    </div>

    @if(($dashboardRole ?? 'student') === 'admin')
        <div class="grid-4">
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Users</div><div class="stat-value">{{ $userCount }}</div><div class="stat-change up"><i class="uil uil-user-check"></i> Active: {{ $activeUserCount }}</div></div><div class="stat-icon si-blue"><i class="uil uil-user-circle"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Online Now</div><div class="stat-value">{{ $onlineUserCount }}</div><div class="stat-change up"><i class="uil uil-wifi"></i> Last 5 minutes</div></div><div class="stat-icon si-green"><i class="uil uil-signal-alt-3"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Groups</div><div class="stat-value">{{ $groupCount }}</div><div class="stat-change up"><i class="uil uil-users-alt"></i> Active: {{ $activeGroupCount }}</div></div><div class="stat-icon si-amber"><i class="uil uil-users-alt"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Queue</div><div class="stat-value">{{ $jobsPending }}</div><div class="stat-change {{ ($failedJobs ?? 0) > 0 ? 'down' : 'up' }}"><i class="uil uil-bolt"></i> Failed: {{ $failedJobs }}</div></div><div class="stat-icon si-red"><i class="uil uil-server"></i></div></div></div>
        </div>

        <div class="grid-7030">
            <div class="card" style="padding:18px">
                <div class="section-header" style="margin-bottom:12px">
                    <div class="section-title" style="font-size:15px">Operational Snapshot</div>
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                        <span class="badge badge-blue"><i class="uil uil-folder me-1"></i> Projects: {{ $projectCount }} (Active: {{ $activeProjectCount }})</span>
                        <span class="badge badge-amber"><i class="uil uil-check-circle me-1"></i> Tasks: {{ $taskCount }}</span>
                        <span class="badge badge-gray"><i class="uil uil-comment-dots me-1"></i> Unread msgs: {{ (int) ($unreadMessages ?? 0) }}</span>
                    </div>
                </div>

                <div class="grid-2" style="gap:12px">
                    <div class="card" style="padding:14px;border:1px solid var(--border);box-shadow:none">
                        <div style="font-weight:800;margin-bottom:8px">Auth Access</div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap">
                            <span class="badge {{ ($authSettings['login_enabled'] ?? true) ? 'badge-green' : 'badge-red' }}"><i class="uil uil-sign-in-alt me-1"></i> Login {{ ($authSettings['login_enabled'] ?? true) ? 'Enabled' : 'Disabled' }}</span>
                            <span class="badge {{ ($authSettings['password_reset_enabled'] ?? true) ? 'badge-green' : 'badge-red' }}"><i class="uil uil-key-skeleton me-1"></i> Reset {{ ($authSettings['password_reset_enabled'] ?? true) ? 'Enabled' : 'Disabled' }}</span>
                            <span class="badge {{ ($authSettings['registration_enabled'] ?? true) ? 'badge-green' : 'badge-red' }}"><i class="uil uil-user-plus me-1"></i> Register {{ ($authSettings['registration_enabled'] ?? true) ? 'Enabled' : 'Disabled' }}</span>
                        </div>
                    </div>
                    <div class="card" style="padding:14px;border:1px solid var(--border);box-shadow:none">
                        <div style="font-weight:800;margin-bottom:8px">Tasks by Status</div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap">
                            <span class="badge badge-gray">Todo: {{ (int) ($taskStatusCounts['todo'] ?? 0) }}</span>
                            <span class="badge badge-blue">In progress: {{ (int) ($taskStatusCounts['in_progress'] ?? 0) }}</span>
                            <span class="badge badge-amber">Review: {{ (int) ($taskStatusCounts['review'] ?? 0) }}</span>
                            <span class="badge badge-green">Completed: {{ (int) ($taskStatusCounts['completed'] ?? 0) }}</span>
                        </div>
                    </div>
                </div>

                <div style="margin-top:14px" class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Recent Users</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach(($recentUsers ?? collect()) as $u)
                            <tr>
                                <td><strong>{{ $u->name }}</strong></td>
                                <td>{{ $u->email }}</td>
                                <td><span class="badge {{ $u->status === 'active' ? 'badge-green' : 'badge-amber' }}">{{ ucfirst($u->status) }}</span></td>
                                <td>{{ optional($u->created_at)->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card" style="padding:18px">
                <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-users-alt me-2"></i> Recent Groups</div>
                <div style="display:grid;gap:10px">
                    @foreach(($recentGroups ?? collect()) as $g)
                        <div class="task-card">
                            <div class="task-title">{{ $g->name }}</div>
                            <div class="task-meta" style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap">
                                <div style="color:var(--text-muted)">
                                    {{ $g->project?->title ?? 'No project' }}
                                </div>
                                <div style="display:flex;gap:6px;align-items:center">
                                    <span class="badge badge-gray">{{ $g->supervisor?->name ?? 'No supervisor' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    @elseif(($dashboardRole ?? 'student') === 'supervisor')
        <div class="grid-4">
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">My Groups</div><div class="stat-value">{{ (int) ($myGroupCount ?? 0) }}</div><div class="stat-change up"><i class="uil uil-users-alt"></i> Assigned</div></div><div class="stat-icon si-blue"><i class="uil uil-users-alt"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Active Projects</div><div class="stat-value">{{ (int) ($activeProjectCount ?? 0) }}</div><div class="stat-change up"><i class="uil uil-folder"></i> In progress</div></div><div class="stat-icon si-green"><i class="uil uil-folder"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Overdue Projects</div><div class="stat-value">{{ (int) ($overdueProjectCount ?? 0) }}</div><div class="stat-change down"><i class="uil uil-exclamation-triangle"></i> Needs action</div></div><div class="stat-icon si-red"><i class="uil uil-exclamation-triangle"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Overdue Tasks</div><div class="stat-value">{{ (int) ($overdueTaskCount ?? 0) }}</div><div class="stat-change {{ ((int) ($pendingReviewTasks ?? 0)) > 0 ? 'down' : 'up' }}"><i class="uil uil-clipboard-notes"></i> In review: {{ (int) ($pendingReviewTasks ?? 0) }}</div></div><div class="stat-icon si-amber"><i class="uil uil-clipboard-notes"></i></div></div></div>
        </div>

        <div class="grid-7030">
            <div class="card" style="padding:18px">
                <div class="section-header" style="margin-bottom:12px">
                    <div class="section-title" style="font-size:15px">My Latest Groups</div>
                    <a href="{{ route('my_group') }}" class="btn btn-outline btn-sm">Open Groups</a>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Group</th>
                            <th>Project</th>
                            <th>Members</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach(($recentGroups ?? collect()) as $g)
                            <tr>
                                <td><strong>{{ $g->name }}</strong></td>
                                <td>{{ $g->project?->title ?? '—' }}</td>
                                <td>{{ (int) ($g->active_members_count ?? 0) }}</td>
                                <td><span class="badge {{ $g->status === 'active' ? 'badge-green' : 'badge-amber' }}">{{ ucfirst($g->status) }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card" style="padding:18px">
                <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-star me-2"></i> Recent Peer Evaluations</div>
                <div style="display:grid;gap:10px">
                    @forelse(($recentEvaluations ?? collect()) as $e)
                        <div class="task-card">
                            <div class="task-title">{{ $e->project?->title ?? 'Project' }}</div>
                            <div class="task-meta" style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap">
                                <div style="color:var(--text-muted)">
                                    {{ $e->evaluator?->name ?? 'Student' }} rated {{ $e->evaluated?->name ?? 'Member' }}
                                </div>
                                <span class="badge badge-blue">{{ number_format((float) $e->overall_score, 2) }}/5</span>
                            </div>
                        </div>
                    @empty
                        <div style="color:var(--text-muted)">No submitted peer evaluations yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

    @else
        <div class="grid-4">
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">My Group</div><div class="stat-value">{{ $group ? $group->name : '—' }}</div><div class="stat-change up"><i class="uil uil-users-alt"></i> {{ $group ? 'Active' : 'Not assigned' }}</div></div><div class="stat-icon si-blue"><i class="uil uil-users-alt"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Tasks</div><div class="stat-value">{{ (int) ($todoTaskCount ?? 0) }}</div><div class="stat-change up"><i class="uil uil-list-ul"></i> Need attention</div></div><div class="stat-icon si-amber"><i class="uil uil-check-circle"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Due Soon</div><div class="stat-value">{{ (int) ($dueSoonCount ?? 0) }}</div><div class="stat-change {{ ((int) ($dueSoonCount ?? 0)) > 0 ? 'down' : 'up' }}"><i class="uil uil-calendar-alt"></i> Next 7 days</div></div><div class="stat-icon si-red"><i class="uil uil-calendar-alt"></i></div></div></div>
            <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Peer Eval</div><div class="stat-value">{{ (int) ($evaluationPending ?? 0) }}</div><div class="stat-change up"><i class="uil uil-star"></i> Pending</div></div><div class="stat-icon si-green"><i class="uil uil-star"></i></div></div></div>
        </div>

        <div class="grid-7030">
            <div class="card" style="padding:18px">
                <div class="section-header" style="margin-bottom:12px">
                    <div class="section-title" style="font-size:15px">My Work</div>
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                        <span class="badge {{ ($surveyCompleted ?? false) ? 'badge-green' : 'badge-amber' }}"><i class="uil uil-clipboard-notes me-1"></i> Skills Survey: {{ ($surveyCompleted ?? false) ? 'Completed' : 'Pending' }}</span>
                        <span class="badge badge-gray"><i class="uil uil-comment-dots me-1"></i> Unread: {{ (int) ($unreadMessages ?? 0) }}</span>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>Task</th>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Due</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse(($upcomingTasks ?? collect()) as $t)
                            <tr>
                                <td><strong>{{ $t->title }}</strong></td>
                                <td>{{ $t->project?->title ?? '—' }}</td>
                                <td><span class="badge badge-blue">{{ str_replace('_', ' ', ucfirst($t->status)) }}</span></td>
                                <td>{{ $t->due_date ? $t->due_date->format('M d, Y') : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="color:var(--text-muted)">No assigned tasks yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card" style="padding:18px">
                <div class="section-title" style="font-size:15px;margin-bottom:12px"><i class="uil uil-users-alt me-2"></i> Group Members</div>
                @if(!$group)
                    <div style="color:var(--text-muted)">You are not assigned to a group yet.</div>
                @else
                    <div style="display:grid;gap:10px">
                        @foreach(($groupMembers ?? collect()) as $m)
                            <div class="task-card">
                                <div class="task-title" style="display:flex;align-items:center;gap:10px">
                                    <span class="av" style="width:34px;height:34px;border-radius:12px;background:var(--primary);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:900">{{ $m->initials }}</span>
                                    <span>{{ $m->name }}</span>
                                </div>
                                <div class="task-meta" style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap">
                                    <span class="badge badge-gray">{{ $m->getRoleNames()->first() ?? 'member' }}</span>
                                    <span class="badge {{ $m->is_online ? 'badge-green' : 'badge-amber' }}">{{ $m->is_online ? 'Online' : 'Offline' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const greetingText = document.getElementById('greeting-text');
    const greetingIcon = document.getElementById('greeting-icon');
    const hour = new Date().getHours();

    let greeting = "Good evening";
    let icon = '<i class="uil uil-moon" style="color: #8b5cf6;"></i>';

    if (hour >= 5 && hour < 12) {
        greeting = "Good morning";
        icon = '<i class="uil uil-sun" style="color: #f59e0b;"></i>';
    } else if (hour >= 12 && hour < 18) {
        greeting = "Good afternoon";
        icon = '<i class="uil uil-cloud-sun" style="color: #fbbf24;"></i>';
    }

    if (greetingText) greetingText.innerText = greeting;
    if (greetingIcon) greetingIcon.innerHTML = icon;
});
</script>
@endpush
