@extends('layouts.app')

@section('breadcrumb', 'Group Management')

@section('content')
<div class="page active" id="page-admin-groups">
    <div class="section-header">
        <div><div class="section-title">Group Management</div><div class="section-sub">Oversee all project groups, members, and supervisors</div></div>
        <div style="display: flex; gap: 8px;">
            <button class="btn btn-outline btn-sm text-danger" onclick="deleteAllGroups()"><i class="uil uil-trash-alt me-1"></i> Delete All Groups</button>
            <button class="btn btn-primary btn-sm" onclick="openModal('modal-group')"><i class="uil uil-plus me-1"></i> Create Group</button>
        </div>
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

<!-- ═══════════════ GROUP PREVIEW MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-group-preview">
    <div class="modal" style="max-width: 550px;">
        <div class="modal-header">
            <span class="modal-title" id="preview-group-name">Group Details</span>
            <span class="modal-close" onclick="closeModal('modal-group-preview')"><i class="uil uil-multiply"></i></span>
        </div>
        <div id="group-preview-content" style="padding: 20px;">
            <div style="display: flex; justify-content: center; padding: 40px;">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    let groupSearch, tableBody, paginationLinks, searchTimeout;

    document.addEventListener('DOMContentLoaded', function() {
        groupSearch = document.getElementById('group-search');
        tableBody = document.getElementById('groups-table-body');
        paginationLinks = document.getElementById('pagination-links');

        if (groupSearch) {
            groupSearch.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => window.fetchGroups(), 300);
            });
        }
        
        attachPaginationListeners();
    });

    window.fetchGroups = function(page = 1) {
        if (!groupSearch || !tableBody || !paginationLinks) return;
        const search = groupSearch.value;
        fetch(`/admin/groups/search?search=${search}&page=${page}`)
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = data.html;
                paginationLinks.innerHTML = data.pagination;
                attachPaginationListeners();
            })
            .catch(err => console.error('Error fetching groups:', err));
    };

    function attachPaginationListeners() {
        if (!paginationLinks) return;
        paginationLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page');
                window.fetchGroups(page);
            });
        });
    }

    window.showGroupDetails = function(groupId) {
        if (window.openModal) window.openModal('modal-group-preview');
        const content = document.getElementById('group-preview-content');
        if (content) content.innerHTML = '<div style="display: flex; justify-content: center; padding: 40px;"><div class="spinner"></div></div>';

        fetch(`/groups/${groupId}`)
            .then(response => response.json())
            .then(group => {
                const previewName = document.getElementById('preview-group-name');
                if (previewName) previewName.innerText = group.name;
                
                const supervisorName = group.supervisor ? group.supervisor.name : 'Unassigned';
                const projectTitle = group.project ? group.project.title : 'No Project Assigned';
                
                let membersHtml = group.members.length > 0 ? group.members.map(m => {
                    if (!m.user) return '';
                    
                    const userSkills = m.user.skills || [];
                    const surveyedSkills = m.user.surveyed_skills || [];
                    const allSkills = [...new Set([...userSkills, ...surveyedSkills.map(s => s.name)])];
                    
                    const skillsBadgeHtml = allSkills.length > 0 
                        ? `<div style="display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px;">
                            ${allSkills.map(s => `<span class="badge" style="font-size: 9px; padding: 1px 6px; background: rgba(37,99,235,0.08); color: var(--primary); border: 1px solid rgba(37,99,235,0.15)">${s}</span>`).join('')}
                           </div>` 
                        : '';

                    return `
                        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--bg-alt); border-radius: 10px; margin-bottom: 6px; border: 1px solid var(--border);">
                            <div class="sidebar-avatar" style="width: 28px; height: 28px; background: ${m.user.avatar ? `url(/${m.user.avatar}) center/cover` : 'var(--primary)'}">${m.user.avatar ? '' : m.user.initials}</div>
                            <div style="flex: 1;">
                                <div style="font-size: 13px; font-weight: 700;">${m.user.name}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">${m.role.toUpperCase()}</div>
                                ${skillsBadgeHtml}
                            </div>
                        </div>
                    `;
                }).join('') : '<div style="color: var(--text-muted); font-style: italic;">No members joined yet.</div>';

                if (content) {
                    content.innerHTML = `
                        <div style="margin-bottom: 20px;">
                            <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Project</div>
                            <div style="font-size: 15px; font-weight: 700; color: var(--primary);">${projectTitle}</div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div style="padding: 12px; background: var(--bg-alt); border-radius: 10px; border: 1px solid var(--border);">
                                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Supervisor</div>
                                <div style="font-size: 13px; font-weight: 600;">${supervisorName}</div>
                            </div>
                            <div style="padding: 12px; background: var(--bg-alt); border-radius: 10px; border: 1px solid var(--border);">
                                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Members Capacity</div>
                                <div style="font-size: 13px; font-weight: 600;">${group.members.length} / ${group.max_members}</div>
                            </div>
                        </div>
                        <div>
                            <h4 style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid var(--border); padding-bottom: 5px;">Group Members</h4>
                            <div style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
                                ${membersHtml}
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 25px; padding-top: 15px; border-top: 1px solid var(--border);">
                            <button class="btn btn-outline" onclick="window.closeModal('modal-group-preview')">Close</button>
                            <a href="{{ route('messages') }}?type=group&id=${group.id}" class="btn btn-primary">
                                <i class="uil uil-comments me-2"></i> Open Group Chat
                            </a>
                            <button class="btn btn-primary" onclick="window.openEditGroupModal(${group.id})">Edit Group</button>
                        </div>
                    `;
                }
            });
    };

    window.openEditGroupModal = function(groupId) {
        if (window.closeModal) window.closeModal('modal-group-preview');
        if (window.openModal) window.openModal('modal-admin-group-edit');
        fetch(`/groups/${groupId}`)
            .then(response => response.json())
            .then(group => {
                const fields = {
                    'edit-group-id': group.id,
                    'edit-group-name': group.name,
                    'edit-group-project': group.project ? group.project.id : '',
                    'edit-group-max': group.max_members,
                    'edit-group-status': group.status
                };
                for (let id in fields) {
                    const el = document.getElementById(id);
                    if (el) el.value = fields[id];
                }
            });
    };

    const adminGroupEditForm = document.getElementById('adminGroupEditForm');
    if (adminGroupEditForm) {
        adminGroupEditForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const groupId = document.getElementById('edit-group-id').value;
            const formData = new FormData(this);
            
            fetch(`/admin/groups/${groupId}/update`, {
                method: 'POST',
                body: formData,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (window.toast) window.toast(data.message, '<i class="uil uil-check-circle"></i>');
                    if (window.closeModal) window.closeModal('modal-admin-group-edit');
                    window.fetchGroups();
                }
            });
        });
    }

    window.deleteGroup = function(groupId, groupName) {
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
                    if (window.toast) window.toast(data.message, '<i class="uil uil-check-circle"></i>');
                    window.fetchGroups();
                }
            });
        }
    };

    window.deleteAllGroups = function() {
        const confirmation = confirm('Are you absolutely sure you want to delete ALL groups? This will remove all group assignments and cannot be undone.');
        if (confirmation) {
            const finalConfirmation = confirm('This is your final warning. Type "DELETE" in the next prompt if you are sure.');
            if (finalConfirmation) {
                const typeConfirm = prompt('Please type "DELETE" to confirm:');
                if (typeConfirm === 'DELETE') {
                    fetch('/admin/groups/delete-all', {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (window.toast) window.toast(data.message, '<i class="uil uil-check-circle"></i>');
                            window.fetchGroups();
                        } else {
                            if (window.toast) window.toast(data.message, '<i class="uil uil-exclamation-triangle"></i>');
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        if (window.toast) window.toast('An error occurred while deleting groups.', '<i class="uil uil-exclamation-triangle"></i>');
                    });
                }
            }
        }
    };

    window.openMembersModal = function(groupId) {
        const idField = document.getElementById('member-group-id');
        if (idField) idField.value = groupId;
        if (window.openModal) window.openModal('modal-admin-group-members');
        window.loadMembers(groupId);
    };

    window.loadMembers = function(groupId) {
        const area = document.getElementById('members-list-area');
        if (area) area.innerHTML = '<div class="spinner" style="margin: 20px auto;"></div>';
        
        fetch(`/groups/${groupId}`)
            .then(response => response.json())
            .then(group => {
                if (!area) return;
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
                        <button class="btn btn-ghost btn-sm text-danger" onclick="window.removeMember(${m.id}, ${groupId})"><i class="uil uil-times"></i></button>
                    </div>
                `).join('');
                area.innerHTML = html;
            });
    };

    const addMemberForm = document.getElementById('addMemberForm');
    if (addMemberForm) {
        addMemberForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const groupId = document.getElementById('member-group-id').value;
            const formData = new FormData(this);
            
            fetch(`/admin/groups/${groupId}/add-member`, {
                method: 'POST',
                body: formData,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (window.toast) window.toast(data.message, '<i class="uil uil-check-circle"></i>');
                    window.loadMembers(groupId);
                    window.fetchGroups();
                } else {
                    if (window.toast) window.toast(data.message, '<i class="uil uil-exclamation-triangle"></i>');
                }
            });
        });
    }

    window.removeMember = function(memberId, groupId) {
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
                    if (window.toast) window.toast(data.message, '<i class="uil uil-check-circle"></i>');
                    window.loadMembers(groupId);
                    window.fetchGroups();
                }
            });
        }
    };

    window.openAssignSupervisorModal = function(groupId, currentSupId) {
        const idField = document.getElementById('supervisor-group-id');
        const selectField = document.getElementById('supervisor-select-field');
        if (idField) idField.value = groupId;
        if (selectField) selectField.value = currentSupId || '';
        if (window.openModal) window.openModal('modal-admin-assign-supervisor');
    };

    const assignSupervisorForm = document.getElementById('assignSupervisorForm');
    if (assignSupervisorForm) {
        assignSupervisorForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const groupId = document.getElementById('supervisor-group-id').value;
            const formData = new FormData(this);
            
            fetch(`/admin/groups/${groupId}/assign-supervisor`, {
                method: 'POST',
                body: formData,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (window.toast) window.toast(data.message, '<i class="uil uil-check-circle"></i>');
                    if (window.closeModal) window.closeModal('modal-admin-assign-supervisor');
                    window.fetchGroups();
                }
            });
        });
    }
})();
</script>
@endpush
