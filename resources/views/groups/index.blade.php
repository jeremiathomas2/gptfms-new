@extends('layouts.app')

@section('breadcrumb', 'Groups')

@section('content')
<div class="page active" id="page-groups">
    <div class="section-header">
        <div><div class="section-title">Group Management</div><div class="section-sub">Manage and monitor all project groups</div></div>
        <div style="display:flex;gap:8px">
            <button class="btn btn-outline btn-sm"><i class="uil uil-robot me-1"></i> Auto-Form Groups</button>
            <button class="btn btn-primary btn-sm" onclick="openModal('modal-group')"><i class="uil uil-plus me-1"></i> Create Group</button>
        </div>
    </div>
    <div class="grid-3">
        @foreach($groups as $group)
        <div class="card" onclick="showGroupDetails({{ $group->id }})">
            <div class="group-card-header">
                <span class="group-name">{{ $group->name }}</span>
                <span class="badge {{ $group->status === 'active' ? 'badge-green' : 'badge-amber' }}">{{ ucfirst($group->status) }}</span>
            </div>
            <div class="group-meta">{{ $group->project->course_code ?? 'N/A' }} · {{ $group->members->count() }} members</div>
            <div class="avatar-group" style="margin-bottom:10px">
                @foreach($group->members->take(3) as $member)
                    <div class="av" title="{{ $member->user->name }}">{{ $member->user->initials }}</div>
                @endforeach
                @if($group->members->count() > 3)
                    <div class="av">+{{ $group->members->count() - 3 }}</div>
                @endif
            </div>
            <div class="skill-tags">
                @php
                    $skills = ['React', 'Node.js', 'PostgreSQL', 'Laravel', 'Python'];
                    shuffle($skills);
                @endphp
                @foreach(array_slice($skills, 0, 3) as $skill)
                    <span class="badge badge-blue">{{ $skill }}</span>
                @endforeach
            </div>
            <div class="progress-wrap">
                @php $progress = $group->project->progress_percentage ?? rand(10, 90); @endphp
                <div class="progress-label"><span>Progress</span><span>{{ $progress }}%</span></div>
                <div class="progress-bar"><div class="progress-fill" style="width:{{ $progress }}%"></div></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: auto;">
                <button class="btn btn-outline btn-sm" style="font-size: 11px;" onclick="event.stopPropagation(); showGroupDetails({{ $group->id }})">
                    <i class="uil uil-eye me-1"></i> Details
                </button>
                <a href="{{ route('messages') }}?type=group&id={{ $group->id }}" class="btn btn-primary btn-sm" style="font-size: 11px;" onclick="event.stopPropagation()">
                    <i class="uil uil-comments me-1"></i> Chat
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="pagination-container">
        {{ $groups->links() }}
    </div>
</div>

<!-- ═══════════════ GROUP DETAILS MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-group-details">
    <div class="modal" style="max-width: 600px;">
        <div class="modal-header">
            <span class="modal-title" id="details-group-name">Group Details</span>
            <span class="modal-close" onclick="closeModal('modal-group-details')"><i class="uil uil-multiply"></i></span>
        </div>
        <div id="group-details-content">
            <div style="display: flex; justify-content: center; padding: 40px;">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showGroupDetails(groupId) {
        openModal('modal-group-details');
        const content = document.getElementById('group-details-content');
        content.innerHTML = '<div style="display: flex; justify-content: center; padding: 40px;"><div class="spinner"></div></div>';

        fetch(`/groups/${groupId}`)
            .then(response => response.json())
            .then(group => {
                document.getElementById('details-group-name').innerText = group.name;
                
                let membersHtml = group.members.map(m => {
                    const userSkills = m.user.skills || [];
                    const surveyedSkills = m.user.surveyed_skills || [];
                    const allSkills = [...new Set([...userSkills, ...surveyedSkills.map(s => s.name)])];
                    
                    const skillsBadgeHtml = allSkills.length > 0 
                        ? `<div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px;">
                            ${allSkills.map(s => `<span class="badge" style="font-size: 9px; padding: 1px 6px; background: rgba(37,99,235,0.08); color: var(--primary); border: 1px solid rgba(37,99,235,0.15)">${s}</span>`).join('')}
                           </div>` 
                        : '';

                    return `
                        <div style="display: flex; align-items: center; gap: 12px; padding: 10px; background: var(--bg-alt); border-radius: 10px; margin-bottom: 8px; border: 1px solid var(--border);">
                            <div class="sidebar-avatar" style="width: 32px; height: 32px; background: ${m.user.avatar ? `url(/${m.user.avatar}) center/cover` : 'var(--primary)'}">${m.user.avatar ? '' : m.user.initials}</div>
                            <div style="flex: 1;">
                                <div style="font-size: 13px; font-weight: 700;">${m.user.name} ${m.user_id == {{ auth()->id() }} ? '<span style="color: var(--primary); font-size: 10px;">(You)</span>' : ''}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">${m.user.registration_number || 'N/A'} · ${m.role.toUpperCase()}</div>
                                ${skillsBadgeHtml}
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 11px; font-weight: 600;">${m.user.email}</div>
                                <div style="font-size: 10px; color: var(--text-muted);">${m.user.phone || ''}</div>
                            </div>
                        </div>
                    `;
                }).join('');

                content.innerHTML = `
                    <div style="padding: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                            <div>
                                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Group Information</div>
                                <h3 style="font-size: 18px; font-weight: 800; margin: 0;">${group.name}</h3>
                                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Formed on ${group.created_at}</div>
                            </div>
                            <span class="badge badge-green">${group.status.toUpperCase()}</span>
                        </div>

                        <div style="margin-bottom: 20px; padding: 15px; background: var(--bg-soft); border-radius: 12px; border-left: 4px solid var(--primary);">
                            <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Project Topic</div>
                            <div style="font-size: 15px; font-weight: 800; color: var(--text);">${group.project ? group.project.title : 'No project assigned'}</div>
                            ${group.project && group.project.description ? `<div style="font-size: 12px; color: var(--text-muted); margin-top: 8px; line-height: 1.5;">${group.project.description}</div>` : ''}
                        </div>

                        ${group.supervisor ? `
                        <div style="margin-bottom: 20px;">
                            <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Supervisor</div>
                            <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg-alt); border-radius: 12px; border: 1px solid var(--border);">
                                <div class="sidebar-avatar" style="width: 40px; height: 40px; background: ${group.supervisor.avatar ? `url(/${group.supervisor.avatar}) center/cover` : 'var(--secondary)'}">${group.supervisor.avatar ? '' : group.supervisor.initials}</div>
                                <div>
                                    <div style="font-size: 14px; font-weight: 700;">${group.supervisor.name}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">${group.supervisor.email}</div>
                                </div>
                            </div>
                        </div>
                        ` : ''}

                        <div style="margin-bottom: 20px;">
                            <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Team Members (${group.members.length} / ${group.max_members})</div>
                            <div style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
                                ${membersHtml}
                            </div>
                        </div>

                        <div style="display: flex; gap: 10px; justify-content: flex-end; padding-top: 15px; border-top: 1px solid var(--border);">
                            <button class="btn btn-outline" onclick="closeModal('modal-group-details')">Close</button>
                            <a href="{{ route('messages') }}?type=group&id=${group.id}" class="btn btn-primary">
                                <i class="uil uil-comments me-2"></i> Open Group Chat
                            </a>
                        </div>
                    </div>
                `;
            });
    }
</script>
@endpush
