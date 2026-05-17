@extends('layouts.app')

@section('breadcrumb', 'Dashboard')

@section('content')
@php
    // User data is now passed from AdminController@dashboard
    $userName = auth()->user() ? auth()->user()->name : 'Guest User';
@endphp
<div class="page active" id="page-dashboard">
    <div class="section-header">
        <div>
            <div class="section-title">Good morning, {{ explode(' ', $userName)[0] }} <i class="uil uil-sun" style="color: #f59e0b;"></i></div>
            <div class="section-sub">Here's what's happening with the system today.</div>
        </div>
        <div style="display:flex;gap:8px">
            <button class="btn btn-outline btn-sm"><i class="uil uil-export me-1"></i> Export</button>
            <button class="btn btn-primary btn-sm" onclick="openModal('modal-group')"><i class="uil uil-plus me-1"></i> New Group</button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid-4">
        <div class="card">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value">{{ $userCount }}</div>
                    <div class="stat-change up"><i class="uil uil-arrow-up"></i> Active members</div>
                </div>
                <div class="stat-icon si-blue"><i class="uil uil-user-circle"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Active Groups</div>
                    <div class="stat-value">{{ $groupCount }}</div>
                    <div class="stat-change up"><i class="uil uil-arrow-up"></i> Formed teams</div>
                </div>
                <div class="stat-icon si-amber"><i class="uil uil-users-alt"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Total Projects</div>
                    <div class="stat-value">{{ $projectCount }}</div>
                    <div class="stat-change up"><i class="uil uil-arrow-up"></i> Projects tracked</div>
                </div>
                <div class="stat-icon si-green"><i class="uil uil-folder"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-label">Total Tasks</div>
                    <div class="stat-value">{{ $taskCount }}</div>
                    <div class="stat-change up"><i class="uil uil-arrow-up"></i> Tasks across groups</div>
                </div>
                <div class="stat-icon si-red"><i class="uil uil-check-circle"></i></div>
            </div>
        </div>
    </div>

    <!-- Task Board + Feed -->
    <div class="grid-7030">
        <!-- Mini Kanban -->
        <div class="card" style="padding:18px">
            <div class="section-header" style="margin-bottom:14px">
                <div class="section-title" style="font-size:15px">Task Board</div>
                <a href="{{ route('tasks') }}" class="btn btn-outline btn-sm">View all</a>
            </div>
            <div class="kanban">
                <div class="kanban-col">
                    <div class="kanban-col-header"><span class="kanban-col-title"><i class="uil uil-list-ul me-1"></i> To Do</span><span class="kanban-count">3</span></div>
                    <div class="task-card"><div class="task-title">Write project proposal</div><div class="task-meta"><span class="badge badge-amber">Soon</span><div class="task-due"><i class="uil uil-calendar-alt"></i> Jun 2</div></div></div>
                    <div class="task-card"><div class="task-title">Finalize ER diagram</div><div class="task-meta"><span class="badge badge-blue">Design</span><div class="task-due"><i class="uil uil-calendar-alt"></i> Jun 5</div></div></div>
                </div>
                <div class="kanban-col">
                    <div class="kanban-col-header"><span class="kanban-col-title"><i class="uil uil-spinner me-1"></i> In Progress</span><span class="kanban-count">2</span></div>
                    <div class="task-card"><div class="task-title">Backend API setup</div><div class="task-meta"><div class="avatar-group"><div class="av">JD</div><div class="av">AK</div></div><div class="task-due"><i class="uil uil-calendar-alt"></i> Jun 4</div></div></div>
                </div>
                <div class="kanban-col">
                    <div class="kanban-col-header"><span class="kanban-col-title"><i class="uil uil-check-circle me-1"></i> Done</span><span class="kanban-count">4</span></div>
                    <div class="task-card" style="opacity:.65"><div class="task-title">Requirements doc</div><div class="task-meta"><span class="badge badge-green">Done</span></div></div>
                </div>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="card">
            <div class="section-title" style="font-size:15px;margin-bottom:14px"><i class="uil uil-history me-2"></i> Activity Feed</div>
            <div class="activity-item">
                <div class="activity-dot" style="background:var(--secondary)"></div>
                <div><div class="activity-text"><strong>Aisha K.</strong> completed "Auth module"</div><div class="activity-time">5 min ago</div></div>
            </div>
            <div class="activity-item">
                <div class="activity-dot" style="background:var(--primary)"></div>
                <div><div class="activity-text"><strong>You</strong> commented on task "ER Diagram"</div><div class="activity-time">20 min ago</div></div>
            </div>
            <div class="activity-item">
                <div class="activity-dot" style="background:var(--danger)"></div>
                <div><div class="activity-text">Deadline: "Prototype" in <strong>3 days</strong></div><div class="activity-time">System alert</div></div>
            </div>
        </div>
    </div>

    <!-- Progress Overview -->
    <div class="grid-2">
        <div class="card">
            <div class="section-title" style="font-size:14px;margin-bottom:14px"><i class="uil uil-pathway me-2"></i> Project Progress</div>
            <div class="timeline">
                <div class="timeline-item"><div class="timeline-dot done"></div><div class="timeline-label">Requirements & Planning</div><div class="timeline-date">Completed · May 1</div></div>
                <div class="timeline-item"><div class="timeline-dot done"></div><div class="timeline-label">Design & Wireframes</div><div class="timeline-date">Completed · May 15</div></div>
                <div class="timeline-item"><div class="timeline-dot" style="background:var(--primary)"></div><div class="timeline-label">Backend Development</div><div class="timeline-date">In progress · Due Jun 5</div></div>
                <div class="timeline-item"><div class="timeline-dot todo"></div><div class="timeline-label">Frontend Integration</div><div class="timeline-date">Upcoming · Jun 15</div></div>
            </div>
        </div>
        <div class="card">
            <div class="section-title" style="font-size:14px;margin-bottom:14px"><i class="uil uil-chart-growth me-2"></i> Skill Distribution</div>
            <div style="display:grid;gap:10px">
                <div><div class="progress-label"><span>Frontend</span><span>80%</span></div><div class="progress-bar"><div class="progress-fill" style="width:80%;background:linear-gradient(90deg,var(--primary),#06B6D4)"></div></div></div>
                <div><div class="progress-label"><span>Backend</span><span>65%</span></div><div class="progress-bar"><div class="progress-fill" style="width:65%;background:linear-gradient(90deg,var(--secondary),#84CC16)"></div></div></div>
            </div>
        </div>
    </div>
</div>
@endsection
