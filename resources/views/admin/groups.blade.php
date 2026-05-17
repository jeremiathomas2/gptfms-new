@extends('layouts.app')

@section('breadcrumb', 'Group Management')

@section('content')
<div class="page active" id="page-admin-groups">
    <div class="section-header">
        <div><div class="section-title">Group Management</div><div class="section-sub">Oversee all project groups, members, and supervisors</div></div>
        <button class="btn btn-primary btn-sm" onclick="openModal('modal-group')"><i class="uil uil-plus me-1"></i> Create Group</button>
    </div>

    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:center;">
            <div class="navbar-search" style="flex:1; max-width:300px;">
                <span><i class="uil uil-search"></i></span>
                <input id="group-search" class="form-control" style="border:none;padding:7px 0;" placeholder="Search groups by name…"/>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Group Name</th><th>Project</th><th>Supervisor</th><th>Members</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="groups-table-body">
                    @include('admin._groups_table')
                </tbody>
            </table>
        </div>
        <div class="pagination-container" id="pagination-links">
            {{ $groups->links() }}
        </div>
    </div>
</div>

<!-- ═══════════════ GROUP EDIT MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-admin-group-edit">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header">
            <span class="modal-title">Edit Group Details</span>
            <span class="modal-close" onclick="closeModal('modal-admin-group-edit')"><i class="uil uil-multiply"></i></span>
        </div>
        <form id="adminGroupEditForm">
            @csrf
            <input type="hidden" id="edit-group-id">
            <div style="padding: 20px;">
                <div class="form-group">
                    <label class="form-label">Group Name</label>
                    <input type="text" class="form-control" id="edit-group-name" name="name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select class="form-control" id="edit-group-project" name="project_id">
                        <option value="">No Project Assigned</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Max Members</label>
                        <input type="number" class="form-control" id="edit-group-max" name="max_members" min="2" max="20" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="edit-group-status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modal-admin-group-edit')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════ GROUP MEMBERS MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-admin-group-members">
    <div class="modal" style="max-width: 600px;">
        <div class="modal-header">
            <span class="modal-title">Manage Group Members</span>
            <span class="modal-close" onclick="closeModal('modal-admin-group-members')"><i class="uil uil-multiply"></i></span>
        </div>
        <div style="padding: 20px;">
            <div id="members-list-area" style="margin-bottom: 25px;">
                <!-- Members list will be loaded here -->
            </div>

            <div style="border-top: 1px solid var(--border); padding-top: 20px;">
                <h4 style="font-size: 13px; font-weight: 700; margin-bottom: 12px;">Add New Member</h4>
                <form id="addMemberForm" style="display: grid; grid-template-columns: 1fr 120px auto; gap: 10px;">
                    @csrf
                    <input type="hidden" id="member-group-id">
                    <select class="form-control" name="user_id" required>
                        <option value="">Select Student</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->registration_number }})</option>
                        @endforeach
                    </select>
                    <select class="form-control" name="role" required>
                        <option value="member">Member</option>
                        <option value="leader">Leader</option>
                    </select>
                    <button type="submit" class="btn btn-primary"><i class="uil uil-plus"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ ASSIGN SUPERVISOR MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-admin-assign-supervisor">
    <div class="modal" style="max-width: 400px;">
        <div class="modal-header">
            <span class="modal-title">Assign Supervisor</span>
            <span class="modal-close" onclick="closeModal('modal-admin-assign-supervisor')"><i class="uil uil-multiply"></i></span>
        </div>
        <form id="assignSupervisorForm">
            @csrf
            <input type="hidden" id="supervisor-group-id">
            <div style="padding: 20px;">
                <div class="form-group">
                    <label class="form-label">Select Supervisor</label>
                    <select class="form-control" name="supervisor_id" required id="supervisor-select-field">
                        <option value="">Choose a Supervisor</option>
                        @foreach($supervisors as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modal-admin-assign-supervisor')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Now</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
const groupSearch = document.getElementById('group-search');
const tableBody = document.getElementById('groups-table-body');
const paginationLinks = document.getElementById('pagination-links');

let searchTimeout;

function fetchGroups(page = 1) {
    const search = groupSearch.value;
    fetch(`/admin/groups/search?search=${search}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = data.html;
            paginationLinks.innerHTML = data.pagination;
            attachPaginationListeners();
        });
}

function attachPaginationListeners() {
    paginationLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(this.href);
            const page = url.searchParams.get('page');
            fetchGroups(page);
        });
    });
}

groupSearch.addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchGroups(), 300);
});

function openEditGroupModal(groupId) {
    openModal('modal-admin-group-edit');
    fetch(`/groups/${groupId}`)
        .then(response => response.json())
        .then(group => {
            document.getElementById('edit-group-id').value = group.id;
            document.getElementById('edit-group-name').value = group.name;
            document.getElementById('edit-group-project').value = group.project ? group.project.id : '';
            document.getElementById('edit-group-max').value = group.max_members;
            document.getElementById('edit-group-status').value = group.status;
        });
}

document.getElementById('adminGroupEditForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const groupId = document.getElementById('edit-group-id').value;
    const formData = new FormData(this);
    
    fetch(`/admin/groups/${groupId}/update`, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toast(data.message, '<i class="uil uil-check-circle"></i>');
            closeModal('modal-admin-group-edit');
            fetchGroups();
        }
    });
});

function deleteGroup(groupId, groupName) {
    if (confirm(`Are you sure you want to delete group "${groupName}"?`)) {
        fetch(`/admin/groups/${groupId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toast(data.message, '<i class="uil uil-check-circle"></i>');
                fetchGroups();
            }
        });
    }
}

function openMembersModal(groupId) {
    document.getElementById('member-group-id').value = groupId;
    openModal('modal-admin-group-members');
    loadMembers(groupId);
}

function loadMembers(groupId) {
    const area = document.getElementById('members-list-area');
    area.innerHTML = '<div class="spinner" style="margin: 20px auto;"></div>';
    
    fetch(`/groups/${groupId}`)
        .then(response => response.json())
        .then(group => {
            if (group.members.length === 0) {
                area.innerHTML = '<div style="text-align: center; color: var(--text-muted); font-size: 13px;">No members in this group.</div>';
                return;
            }
            
            let html = group.members.map(m => `
                <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg-alt); border-radius: 12px; border: 1px solid var(--border); margin-bottom: 8px;">
                    <div class="sidebar-avatar" style="width: 32px; height: 32px; background: ${m.user.avatar ? `url(/${m.user.avatar}) center/cover` : 'var(--primary)'}">${m.user.avatar ? '' : m.user.initials}</div>
                    <div style="flex: 1;">
                        <div style="font-size: 13px; font-weight: 700;">${m.user.name}</div>
                        <div style="font-size: 11px; color: var(--text-muted);">${m.role.toUpperCase()}</div>
                    </div>
                    <button class="btn btn-ghost btn-sm text-danger" onclick="removeMember(${m.id}, ${groupId})"><i class="uil uil-times"></i></button>
                </div>
            `).join('');
            area.innerHTML = html;
        });
}

document.getElementById('addMemberForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const groupId = document.getElementById('member-group-id').value;
    const formData = new FormData(this);
    
    fetch(`/admin/groups/${groupId}/add-member`, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toast(data.message, '<i class="uil uil-check-circle"></i>');
            loadMembers(groupId);
            fetchGroups();
        } else {
            toast(data.message, '<i class="uil uil-exclamation-triangle"></i>');
        }
    });
});

function removeMember(memberId, groupId) {
    if (confirm('Remove this member from the group?')) {
        fetch(`/admin/groups/members/${memberId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toast(data.message, '<i class="uil uil-check-circle"></i>');
                loadMembers(groupId);
                fetchGroups();
            }
        });
    }
}

function openAssignSupervisorModal(groupId, currentSupId) {
    document.getElementById('supervisor-group-id').value = groupId;
    document.getElementById('supervisor-select-field').value = currentSupId || '';
    openModal('modal-admin-assign-supervisor');
}

document.getElementById('assignSupervisorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const groupId = document.getElementById('supervisor-group-id').value;
    const formData = new FormData(this);
    
    fetch(`/admin/groups/${groupId}/assign-supervisor`, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toast(data.message, '<i class="uil uil-check-circle"></i>');
            closeModal('modal-admin-assign-supervisor');
            fetchGroups();
        }
    });
});
</script>
@endpush
