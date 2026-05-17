@foreach($groups as $g)
<tr>
    <td>
        <div style="font-weight: 700;">{{ $g->name }}</div>
        <div style="font-size: 11px; color: var(--text-muted);">Formed: {{ $g->created_at->format('M d, Y') }}</div>
    </td>
    <td>
        <div style="font-size: 13px; font-weight: 600;">{{ $g->project->title ?? 'No Project' }}</div>
        <div style="font-size: 11px; color: var(--text-muted);">{{ $g->project->course_code ?? '' }}</div>
    </td>
    <td>
        @if($g->supervisor)
            <div style="display:flex;align-items:center;gap:8px">
                <div class="sidebar-avatar" style="width:24px;height:24px;font-size:10px;border-radius:6px">
                    {{ $g->supervisor->initials }}
                </div>
                <div style="font-size: 13px; font-weight: 600;">{{ $g->supervisor->name }}</div>
            </div>
        @else
            <span style="font-size: 12px; color: var(--text-muted); font-style: italic;">Unassigned</span>
        @endif
    </td>
    <td>
        <div style="display: flex; align-items: center; gap: 6px;">
            <span style="font-weight: 700; color: var(--primary);">{{ $g->members->count() }}</span>
            <span style="color: var(--text-muted);">/ {{ $g->max_members }}</span>
        </div>
    </td>
    <td><span class="badge {{ $g->status === 'active' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($g->status) }}</span></td>
    <td>
        <div style="display:flex;gap:4px">
            <button class="btn btn-ghost btn-sm" onclick="showGroupDetails({{ $g->id }})" title="View Details"><i class="uil uil-eye"></i></button>
            <button class="btn btn-ghost btn-sm" onclick="openEditGroupModal({{ $g->id }})" title="Edit Group"><i class="uil uil-edit"></i></button>
            <button class="btn btn-ghost btn-sm" onclick="openMembersModal({{ $g->id }})" title="Manage Members"><i class="uil uil-users-alt"></i></button>
            <button class="btn btn-ghost btn-sm" onclick="openAssignSupervisorModal({{ $g->id }}, {{ $g->supervisor_id ?? 'null' }})" title="Assign Supervisor"><i class="uil uil-graduation-cap"></i></button>
            <button class="btn btn-ghost btn-sm text-danger" onclick="deleteGroup({{ $g->id }}, '{{ $g->name }}')" title="Delete Group"><i class="uil uil-trash-alt"></i></button>
        </div>
    </td>
</tr>
@endforeach
