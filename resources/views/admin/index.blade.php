@extends('layouts.app')

@section('breadcrumb', 'Admin Panel')

@section('content')
<div class="page active" id="page-admin">
    <div class="section-header">
        <div><div class="section-title">Admin Panel</div><div class="section-sub">Manage users, roles, and system configuration</div></div>
        <button class="btn btn-primary btn-sm" onclick="toast('User invited!','<i class=\'uil uil-user-plus\'></i>')"><i class="uil uil-user-plus me-1"></i> Invite User</button>
    </div>
    <div class="grid-4" style="margin-bottom:18px">
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Total Users</div><div class="stat-value">{{ $totalUsers }}</div></div><div class="stat-icon si-blue"><i class="uil uil-users-alt"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Students</div><div class="stat-value">{{ $students }}</div></div><div class="stat-icon si-green"><i class="uil uil-graduation-cap"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Supervisors</div><div class="stat-value">{{ $supervisors }}</div></div><div class="stat-icon si-amber"><i class="uil uil-briefcase-alt"></i></div></div></div>
        <div class="card"><div class="stat-card"><div class="stat-info"><div class="stat-label">Admins</div><div class="stat-value">{{ $admins }}</div></div><div class="stat-icon si-red"><i class="uil uil-wrench"></i></div></div></div>
    </div>
    <div class="card" style="padding:0;overflow:hidden">
        <div style="padding:14px 18px;border-bottom:1px solid var(--border)">
            <input class="form-control" style="max-width:280px;padding:7px 12px" placeholder="🔍 Search users by name or email…"/>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Group</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <div class="sidebar-avatar" style="width:28px;height:28px;font-size:11px;border-radius:7px">{{ $u->initials }}</div>
                                <strong>{{ $u->name }}</strong>
                            </div>
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @php $role = $u->roles->first()->name ?? 'N/A'; @endphp
                            <span class="badge {{ $role === 'admin' ? 'badge-red' : ($role === 'supervisor' ? 'badge-amber' : 'badge-blue') }}">
                                {{ ucfirst($role) }}
                            </span>
                        </td>
                        <td>{{ $u->members->first()->group->name ?? '—' }}</td>
                        <td><span class="badge badge-green">{{ ucfirst($u->status ?? 'active') }}</span></td>
                        <td>{{ $u->created_at->format('M d') }}</td>
                        <td>
                            <div style="display:flex;gap:4px">
                                <button class="btn btn-ghost btn-sm" onclick="showUserPreview({{ $u->id }})" title="View Profile"><i class="uil uil-eye"></i></button>
                                <button class="btn btn-ghost btn-sm" onclick="toast('Edit user modal…','<i class=\'uil uil-edit\'></i>')"><i class="uil uil-edit"></i></button>
                                <button class="btn btn-ghost btn-sm" onclick="toast('User deactivated','<i class=\'uil uil-user-times\'></i>')"><i class="uil uil-user-times"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding: 15px;">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- ═══════════════ USER PREVIEW MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-user-preview">
    <div class="modal" style="max-width: 500px;">
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

<script>
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
            
            content.innerHTML = `
                <div style="text-align: center; margin-bottom: 20px;">
                    <div class="sidebar-avatar" style="width: 80px; height: 80px; font-size: 32px; margin: 0 auto 15px; border-radius: 20px;">${user.initials}</div>
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
                            <span style="color: var(--text-muted);">Status:</span>
                            <span class="badge badge-green">${user.status || 'active'}</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; padding-top: 15px; border-top: 1px solid var(--border);">
                    <button class="btn btn-outline" onclick="closeModal('modal-user-preview')">Close</button>
                    <button class="btn btn-primary" onclick="toast('Edit functionality coming soon…','<i class=\'uil uil-edit\'></i>')">Edit User</button>
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = '<div style="text-align: center; padding: 20px; color: var(--danger);">Failed to load user profile.</div>';
        });
}
</script>
@endsection
