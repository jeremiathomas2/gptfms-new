@extends('layouts.app')

@section('breadcrumb', 'Supervisor')

@section('content')
@php
    $totalGroups = \App\Models\Group::count();
    $activeProjects = \App\Models\Project::where('status', 'in_progress')->count();
    $atRiskGroups = \App\Models\Project::where('progress_percentage', '<', 40)->count();
    $pendingReviews = \App\Models\Group::take(3)->get(); // Placeholder logic
@endphp
<div class="page active" id="page-supervisor">
    <div class="section-header">
        <div><div class="section-title">Supervisor Dashboard</div><div class="section-sub">Monitor all groups and intervene when needed</div></div>
        <button class="btn btn-primary btn-sm" onclick="toast('Report generated!','<i class=\'uil uil-file-alt\'></i>')"><i class="uil uil-file-alt me-1"></i> Generate Report</button>
    </div>
    <div class="grid-3">
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Total Groups</div><div class="stat-value">{{ $totalGroups }}</div><div class="stat-change up">All monitored</div></div><div class="stat-icon si-blue"><i class="uil uil-users-alt"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Active Projects</div><div class="stat-value">{{ $activeProjects }}</div><div class="stat-change up"><i class="uil uil-arrow-up"></i> Ongoing</div></div><div class="stat-icon si-green"><i class="uil uil-folder"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">At Risk Groups</div><div class="stat-value">{{ $atRiskGroups }}</div><div class="stat-change down">Action needed</div></div><div class="stat-icon si-red"><i class="uil uil-exclamation-triangle"></i></div></div></div>
    </div>
    <div class="grid-2">
        <div class="card">
            <div class="section-title" style="font-size:14px;margin-bottom:14px"><i class="uil uil-exclamation-octagon me-2" style="color:var(--danger)"></i> Groups Needing Attention</div>
            <div class="activity-item">
                <div class="activity-dot" style="background:var(--danger)"></div>
                <div style="flex:1"><div class="activity-text"><strong>Group Beta</strong> — Only 32% progress · Deadline in 12 days</div><div style="display:flex;gap:6px;margin-top:6px"><button class="btn btn-outline btn-sm">Send Reminder</button><button class="btn btn-danger btn-sm" onclick="toast('Intervention sent to Group Beta','<i class=\'uil uil-megaphone\'></i>')">Intervene</button></div></div>
            </div>
            <div class="activity-item">
                <div class="activity-dot" style="background:var(--accent)"></div>
                <div style="flex:1"><div class="activity-text"><strong>Group Delta</strong> — Team conflict reported · Low participation</div><div style="display:flex;gap:6px;margin-top:6px"><button class="btn btn-outline btn-sm">View Details</button><button class="btn btn-primary btn-sm" onclick="toast('Meeting scheduled!','<i class=\'uil uil-calendar-alt\'></i>')">Schedule Meeting</button></div></div>
            </div>
        </div>
        <div class="card">
            <div class="section-title" style="font-size:14px;margin-bottom:14px"><i class="uil uil-clipboard-notes me-2" style="color:var(--primary)"></i> Pending Reviews</div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Group</th><th>Milestone</th><th>Submitted</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach($pendingReviews as $g)
                        <tr>
                            <td><strong>{{ $g->name }}</strong></td>
                            <td>Milestone {{ rand(1,3) }}</td>
                            <td>May {{ rand(20, 30) }}</td>
                            <td><button class="btn btn-primary btn-sm" onclick="toast('Review opened!','<i class=\'uil uil-eye\'></i>')">Review</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
