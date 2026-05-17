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
        <div class="card" onclick="showGroupPreview({{ $group->id }})">
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
        </div>
        @endforeach
        <div class="card" style="border:2px dashed var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer;flex-direction:column;gap:8px;min-height:180px" onclick="openModal('modal-group')">
            <div style="font-size:32px;color:var(--text-muted)"><i class="uil uil-plus-circle"></i></div>
            <div style="font-size:13px;color:var(--text-muted);font-weight:600">Create New Group</div>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        {{ $groups->links() }}
    </div>
</div>

<!-- ═══════════════ GROUP PREVIEW MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-group-preview">
    <div class="modal" style="max-width: 600px;">
        <div class="modal-header">
            <span class="modal-title" id="preview-group-name">Group Details</span>
            <span class="modal-close" onclick="closeModal('modal-group-preview')"><i class="uil uil-multiply"></i></span>
        </div>
        <div id="group-preview-content">
            <div style="display: flex; justify-content: center; padding: 40px;">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>

<script>
function showGroupPreview(groupId) {
    openModal('modal-group-preview');
    const content = document.getElementById('group-preview-content');
    content.innerHTML = '<div style="display: flex; justify-content: center; padding: 40px;"><div class="spinner"></div></div>';
    
    fetch(`/groups/${groupId}`)
        .then(response => response.json())
        .then(group => {
            document.getElementById('preview-group-name').innerText = group.name;
            
            let membersHtml = '';
            group.members.forEach(member => {
                membersHtml += `
                    <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--bg-alt); border-radius: 8px; margin-bottom: 8px;">
                        <div class="sidebar-avatar" style="width: 32px; height: 32px; font-size: 12px;">${member.user.initials}</div>
                        <div style="flex: 1;">
                            <div style="font-size: 13px; font-weight: 700;">${member.user.name}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">${member.role.charAt(0).toUpperCase() + member.role.slice(1)}</div>
                        </div>
                        <span class="badge ${member.status === 'joined' ? 'badge-green' : 'badge-amber'}" style="font-size: 10px;">${member.status}</span>
                    </div>
                `;
            });

            content.innerHTML = `
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <div>
                            <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Project</div>
                            <div style="font-size: 14px; font-weight: 700;">${group.project ? group.project.title : 'No project assigned'}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Status</div>
                            <span class="badge ${group.status === 'active' ? 'badge-green' : 'badge-amber'}">${group.status.toUpperCase()}</span>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Description</div>
                        <div style="font-size: 13px; line-height: 1.5;">${group.description || 'No description provided.'}</div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        <div style="padding: 12px; background: var(--bg-alt); border-radius: 10px; border: 1px solid var(--border);">
                            <div style="font-size: 11px; color: var(--text-muted);">Max Members</div>
                            <div style="font-size: 16px; font-weight: 800;">${group.max_members}</div>
                        </div>
                        <div style="padding: 12px; background: var(--bg-alt); border-radius: 10px; border: 1px solid var(--border);">
                            <div style="font-size: 11px; color: var(--text-muted);">Current Members</div>
                            <div style="font-size: 16px; font-weight: 800;">${group.members.length}</div>
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Team Members</div>
                        <div style="max-height: 200px; overflow-y: auto;">
                            ${membersHtml || '<p style="font-size: 13px; color: var(--text-muted);">No members yet.</p>'}
                        </div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end; padding-top: 15px; border-top: 1px solid var(--border);">
                    <button class="btn btn-outline" onclick="closeModal('modal-group-preview')">Close</button>
                    @role('student')
                        <button class="btn btn-primary" onclick="toast('Join request sent!','<i class=\'uil uil-user-plus\'></i>')">Request to Join</button>
                    @endrole
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = '<div style="text-align: center; padding: 20px; color: var(--danger);">Failed to load group details.</div>';
        });
}
</script>
@endsection
