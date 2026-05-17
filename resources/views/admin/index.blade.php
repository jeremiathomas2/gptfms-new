@extends('layouts.app')

@section('breadcrumb', 'Admin Panel')

@section('content')
<div class="page active" id="page-admin">
    <div class="section-header">
        <div><div class="section-title">User Management</div><div class="section-sub">Manage users, roles, and system configuration</div></div>
        <div style="display:flex;gap:8px;align-items:center;">
            <div class="dropdown-wrap" style="position:relative;">
                <button class="btn btn-outline btn-sm" onclick="toggleDropdown('template-dropdown')"><i class="uil uil-file-download me-1"></i> Templates</button>
                <div id="template-dropdown" class="dropdown" style="width:200px; right:0; top:35px;">
                    <a href="{{ route('users.template', 'student') }}" class="notif-item"><i class="uil uil-graduation-cap me-2"></i> Student Template</a>
                    <a href="{{ route('users.template', 'supervisor') }}" class="notif-item"><i class="uil uil-briefcase-alt me-2"></i> Supervisor Template</a>
                </div>
            </div>
            <div class="dropdown-wrap" style="position:relative;">
                <button class="btn btn-outline btn-sm" onclick="toggleDropdown('import-dropdown')"><i class="uil uil-file-upload me-1"></i> Import CSV</button>
                <div id="import-dropdown" class="dropdown" style="width:200px; right:0; top:35px;">
                    <a href="#" class="notif-item" onclick="openImportModal('student')"><i class="uil uil-graduation-cap me-2"></i> Import Students</a>
                    <a href="#" class="notif-item" onclick="openImportModal('supervisor')"><i class="uil uil-briefcase-alt me-2"></i> Import Supervisors</a>
                </div>
            </div>
            <button class="btn btn-primary btn-sm" onclick="openAddUserModal()"><i class="uil uil-user-plus me-1"></i> Add User</button>
        </div>
    </div>
    <div class="grid-4" style="margin-bottom:18px">
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Total Users</div><div class="stat-value">{{ $totalUsers }}</div></div><div class="stat-icon si-blue"><i class="uil uil-users-alt"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Students</div><div class="stat-value">{{ $students }}</div></div><div class="stat-icon si-green"><i class="uil uil-graduation-cap"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Supervisors</div><div class="stat-value">{{ $supervisors }}</div></div><div class="stat-icon si-amber"><i class="uil uil-briefcase-alt"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Admins</div><div class="stat-value">{{ $admins }}</div></div><div class="stat-icon si-red"><i class="uil uil-wrench"></i></div></div></div>
    </div>
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:14px 18px;border-bottom:1px solid var(--border);display:flex;gap:12px;align-items:center;">
            <div class="navbar-search" style="flex:1; max-width:300px;">
                <span><i class="uil uil-search"></i></span>
                <input id="user-search" class="form-control" style="border:none;padding:7px 0;" placeholder="Search users by name, email or REG…"/>
            </div>
            <select id="filter-role" class="form-control" style="max-width:150px;">
                <option value="all">All Roles</option>
                <option value="student">Student</option>
                <option value="supervisor">Supervisor</option>
                <option value="admin">Admin</option>
            </select>
            <select id="filter-status" class="form-control" style="max-width:150px;">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
            </select>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Group</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
                <tbody id="users-table-body">
                    @include('admin._users_table')
                </tbody>
            </table>
        </div>
        <div class="pagination-container" id="pagination-links">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- ═══════════════ ADD USER MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-user-add">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header">
            <span class="modal-title">Add New User</span>
            <span class="modal-close" onclick="closeModal('modal-user-add')"><i class="uil uil-multiply"></i></span>
        </div>
        <form id="userAddForm">
            @csrf
            <div style="padding: 20px;">
                <div class="form-row-3">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" required placeholder="First Name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Middle Name</label>
                        <input type="text" class="form-control" name="middle_name" placeholder="Middle Name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" required placeholder="Last Name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required placeholder="Enter email address">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone" placeholder="Enter phone number">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select class="form-control" name="gender" required>
                            <option value="">Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <select class="form-control" name="role" required onchange="toggleRegNumber(this)">
                            <option value="student">Student</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group" id="add-reg-number-group">
                        <label class="form-label">Registration Number</label>
                        <input type="text" class="form-control" name="registration_number" placeholder="Enter REG number">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required placeholder="Enter password (min 8 characters)">
                </div>
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modal-user-add')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════ CSV IMPORT MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-csv-import">
    <div class="modal" style="max-width: 450px;">
        <div class="modal-header">
            <span class="modal-title" id="csv-import-title">Import Users</span>
            <span class="modal-close" onclick="closeModal('modal-csv-import')"><i class="uil uil-multiply"></i></span>
        </div>
        <form id="csvImportForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" id="import-type">
            <div style="padding: 20px; border: 2px dashed var(--border); border-radius: 12px; text-align: center; margin-bottom: 20px;" id="drop-zone">
                <i class="uil uil-cloud-upload" style="font-size: 40px; color: var(--primary); display: block; margin-bottom: 10px;"></i>
                <div style="font-size: 14px; font-weight: 600; margin-bottom: 5px;">Click to upload or drag and drop</div>
                <div style="font-size: 11px; color: var(--text-muted);">CSV file only (Max 5MB)</div>
                <input type="file" name="file" id="csv-file-input" style="display: none;" accept=".csv">
            </div>
            <div id="file-name-display" style="font-size: 12px; font-weight: 600; color: var(--secondary); margin-bottom: 15px; display: none; text-align: center;"></div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-csv-import')">Cancel</button>
                <button type="submit" class="btn btn-primary">Start Import</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════ USER PREVIEW MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-user-preview">
    <div class="modal" style="max-width: 550px;">
        <div class="modal-header">
            <span class="modal-title" id="preview-user-name">User Profile</span>
            <span class="modal-close" onclick="closeModal('modal-user-preview')"><i class="uil uil-multiply"></i></span>
        </div>
        <div id="user-preview-content">
            <div style="display: flex; justify-content: center; padding: 40px;">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════ USER EDIT MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-user-edit">
    <div class="modal" style="max-width: 500px;">
        <div class="modal-header">
            <span class="modal-title">Edit User Details</span>
            <span class="modal-close" onclick="closeModal('modal-user-edit')"><i class="uil uil-multiply"></i></span>
        </div>
        <form id="userEditForm">
            @csrf
            <input type="hidden" id="edit-user-id">
            <div style="padding: 20px;">
                <div class="form-row-3">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" id="edit-first-name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="edit-middle-name" name="middle_name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="edit-last-name" name="last_name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="edit-email" name="email" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" id="edit-phone" name="phone">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select class="form-control" id="edit-gender" name="gender">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Registration Number</label>
                        <input type="text" class="form-control" id="edit-reg-number" name="registration_number">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="edit-status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>

                <div id="password-reset-area" style="margin-top: 15px; padding: 12px; background: rgba(37, 99, 235, 0.1); border-radius: 10px; border: 1px dashed var(--primary); display: none;">
                    <div style="font-size: 11px; color: var(--primary); font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">New Generated Password</div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <code id="new-password-display" style="font-size: 16px; font-weight: 800; color: var(--text); letter-spacing: 1px;"></code>
                        <button type="button" class="btn btn-ghost btn-sm" onclick="copyPassword()"><i class="uil uil-copy"></i></button>
                    </div>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 5px;">Please share this password with the user. It will not be shown again.</div>
                </div>

                <div style="display: flex; gap: 10px; justify-content: space-between; margin-top: 25px; padding-top: 15px; border-top: 1px solid var(--border);">
                    <button type="button" class="btn btn-outline btn-sm text-danger" onclick="resetUserPassword()"><i class="uil uil-key-skeleton me-1"></i> Reset Password</button>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="btn btn-outline btn-sm" onclick="closeModal('modal-user-edit')">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Real-time Search and Filters
const userSearch = document.getElementById('user-search');
const filterRole = document.getElementById('filter-role');
const filterStatus = document.getElementById('filter-status');
const tableBody = document.getElementById('users-table-body');
const paginationLinks = document.getElementById('pagination-links');

let searchTimeout;

function fetchUsers(page = 1) {
    const search = userSearch.value;
    const role = filterRole.value;
    const status = filterStatus.value;

    fetch(`/users/search?search=${search}&role=${role}&status=${status}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = data.html;
            paginationLinks.innerHTML = data.pagination;
            
            // Re-attach pagination link listeners
            attachPaginationListeners();
        });
}

function attachPaginationListeners() {
    paginationLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(this.href);
            const page = url.searchParams.get('page');
            fetchUsers(page);
        });
    });
}

userSearch.addEventListener('input', () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchUsers(), 300);
});

filterRole.addEventListener('change', () => fetchUsers());
filterStatus.addEventListener('change', () => fetchUsers());

// Add User
function openAddUserModal() {
    document.getElementById('userAddForm').reset();
    openModal('modal-user-add');
}

function toggleRegNumber(select) {
    const regGroup = document.getElementById('add-reg-number-group');
    if (select.value === 'student') {
        regGroup.style.display = 'block';
    } else {
        regGroup.style.display = 'none';
    }
}

document.getElementById('userAddForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="uil uil-spinner-alt uil-spin"></i> Creating...';

    fetch("{{ route('users.store') }}", {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Create User';
        
        if (data.success) {
            toast(data.message, '<i class="uil uil-check-circle"></i>');
            closeModal('modal-user-add');
            fetchUsers();
        } else {
            toast(data.message || 'Failed to create user', '<i class="uil uil-exclamation-triangle"></i>');
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Create User';
        console.error('Error:', error);
        toast('An error occurred while creating user.', '<i class="uil uil-exclamation-triangle"></i>');
    });
});

// CSV Import
function openImportModal(type) {
    document.getElementById('import-type').value = type;
    document.getElementById('csv-import-title').innerText = `Import ${type.charAt(0).toUpperCase() + type.slice(1)}s`;
    openModal('modal-csv-import');
}

const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('csv-file-input');
const fileNameDisplay = document.getElementById('file-name-display');

dropZone.addEventListener('click', () => fileInput.click());

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = 'var(--primary)';
});

dropZone.addEventListener('dragleave', () => {
    dropZone.style.borderColor = 'var(--border)';
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = 'var(--border)';
    if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        updateFileName();
    }
});

fileInput.addEventListener('change', updateFileName);

function updateFileName() {
    if (fileInput.files.length) {
        fileNameDisplay.innerText = `Selected: ${fileInput.files[0].name}`;
        fileNameDisplay.style.display = 'block';
    }
}

document.getElementById('csvImportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="uil uil-spinner-alt uil-spin"></i> Importing...';

    fetch("{{ route('users.import') }}", {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Start Import';
        
        if (data.success) {
            toast(data.message, '<i class="uil uil-check-circle"></i>');
            closeModal('modal-csv-import');
            fetchUsers();
            if (data.errors && data.errors.length > 0) {
                console.warn('Import warnings:', data.errors);
                toast(`Completed with ${data.errors.length} warnings.`, '<i class="uil uil-exclamation-circle"></i>');
            }
        } else {
            toast(data.message || 'Import failed', '<i class="uil uil-exclamation-triangle"></i>');
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Start Import';
        console.error('Error:', error);
        toast('An error occurred during import.', '<i class="uil uil-exclamation-triangle"></i>');
    });
});

function deleteUser(userId, userName) {
    if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
        fetch(`/admin/users/${userId}`, {
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
                fetchUsers(); // Refresh the table
            } else {
                toast(data.message || 'Failed to delete user', '<i class="uil uil-exclamation-triangle"></i>');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toast('An error occurred while deleting the user.', '<i class="uil uil-exclamation-triangle"></i>');
        });
    }
}

function showUserPreview(userId) {
    openModal('modal-user-preview');
    const content = document.getElementById('user-preview-content');
    content.innerHTML = '<div style="display: flex; justify-content: center; padding: 40px;"><div class="spinner"></div></div>';
    
    fetch(`/admin/users/${userId}`)
        .then(response => response.json())
        .then(user => {
            document.getElementById('preview-user-name').innerText = user.name;
            
            const role = user.roles[0]?.name || 'User';
            const groupName = user.members[0]?.group?.name || 'No Group';
            const skills = user.surveyed_skills || [];
            
            let skillsHtml = '<div style="color: var(--text-muted); font-style: italic;">No surveyed skills found.</div>';
            if (skills.length > 0) {
                skillsHtml = `<div style="display: flex; flex-wrap: wrap; gap: 6px;">
                    ${skills.map(s => `<span class="badge badge-blue" style="font-size: 11px;">${s.name} (${s.level})</span>`).join('')}
                </div>`;
            }
            
            content.innerHTML = `
                <div style="text-align: center; margin-bottom: 20px;">
                    <div class="sidebar-avatar" style="width: 80px; height: 80px; font-size: 32px; margin: 0 auto 15px; border-radius: 20px; background: ${user.avatar ? `url(/${user.avatar}) center/cover` : 'linear-gradient(135deg,var(--primary),var(--secondary))'}">${user.avatar ? '' : user.initials}</div>
                    <h3 style="font-size: 20px; font-weight: 800; margin-bottom: 5px;">${user.name}</h3>
                    <span class="badge ${role === 'admin' ? 'badge-red' : (role === 'supervisor' ? 'badge-amber' : 'badge-blue')}">${role.toUpperCase()}</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div style="padding: 12px; background: var(--bg-alt); border-radius: 10px; border: 1px solid var(--border);">
                        <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Email</div>
                        <div style="font-size: 13px; font-weight: 600; word-break: break-all;">${user.email}</div>
                    </div>
                    <div style="padding: 12px; background: var(--bg-alt); border-radius: 10px; border: 1px solid var(--border);">
                        <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">Group</div>
                        <div style="font-size: 13px; font-weight: 600;">${groupName}</div>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <h4 style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid var(--border); padding-bottom: 5px;">Profile Details</h4>
                    <div style="display: grid; gap: 8px;">
                        <div style="display: flex; justify-content: space-between; font-size: 13px;">
                            <span style="color: var(--text-muted);">Registration Number:</span>
                            <span style="font-weight: 600;">${user.registration_number || 'N/A'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 13px;">
                            <span style="color: var(--text-muted);">Phone:</span>
                            <span style="font-weight: 600;">${user.phone || 'N/A'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 13px;">
                            <span style="color: var(--text-muted);">Gender:</span>
                            <span style="font-weight: 600; text-transform: capitalize;">${user.gender || 'N/A'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 13px;">
                            <span style="color: var(--text-muted);">Status:</span>
                            <span class="badge badge-green">${user.status || 'active'}</span>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <h4 style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid var(--border); padding-bottom: 5px;">Surveyed Skills</h4>
                    ${skillsHtml}
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; padding-top: 15px; border-top: 1px solid var(--border);">
                    <button class="btn btn-outline" onclick="closeModal('modal-user-preview')">Close</button>
                    <button class="btn btn-primary" onclick="openEditModal(${user.id})">Edit User</button>
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = '<div style="text-align: center; padding: 20px; color: var(--danger);">Failed to load user profile.</div>';
        });
}

function openEditModal(userId) {
    closeModal('modal-user-preview');
    openModal('modal-user-edit');
    
    // Hide password reset area when opening modal
    document.getElementById('password-reset-area').style.display = 'none';
    document.getElementById('new-password-display').innerText = '';

    fetch(`/admin/users/${userId}`)
        .then(response => response.json())
        .then(user => {
            document.getElementById('edit-user-id').value = user.id;
            document.getElementById('edit-first-name').value = user.first_name;
            document.getElementById('edit-middle-name').value = user.middle_name || '';
            document.getElementById('edit-last-name').value = user.last_name;
            document.getElementById('edit-email').value = user.email;
            document.getElementById('edit-phone').value = user.phone || '';
            document.getElementById('edit-gender').value = user.gender || 'male';
            document.getElementById('edit-reg-number').value = user.registration_number || '';
            document.getElementById('edit-status').value = user.status || 'active';
        });
}

document.getElementById('userEditForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const userId = document.getElementById('edit-user-id').value;
    const formData = new FormData(this);
    
    fetch(`/admin/users/${userId}`, {
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
            toast(data.message, '<i class="uil uil-check-circle"></i>');
            closeModal('modal-user-edit');
            fetchUsers(); // Refresh the table
        } else {
            toast('Failed to update user', '<i class="uil uil-exclamation-triangle"></i>');
        }
    });
});

function resetUserPassword() {
    const userId = document.getElementById('edit-user-id').value;
    if (confirm('Are you sure you want to reset this user\'s password? A new random password will be generated.')) {
        fetch(`/admin/users/${userId}/reset-password`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toast(data.message, '<i class="uil uil-check-circle"></i>');
                const area = document.getElementById('password-reset-area');
                const display = document.getElementById('new-password-display');
                area.style.display = 'block';
                display.innerText = data.new_password;
            } else {
                toast(data.message || 'Failed to reset password', '<i class="uil uil-exclamation-triangle"></i>');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toast('An error occurred during password reset.', '<i class="uil uil-exclamation-triangle"></i>');
        });
    }
}

function copyPassword() {
    const password = document.getElementById('new-password-display').innerText;
    navigator.clipboard.writeText(password).then(() => {
        toast('Password copied to clipboard!', '<i class="uil uil-copy"></i>');
    });
}
</script>
@endsection
