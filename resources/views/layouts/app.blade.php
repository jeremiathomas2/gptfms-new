<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GPTFMS') }} — Group Project Team Formation & Management</title>
    <!-- Phoenix Template Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Phoenix Template Icon Fonts -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/gptfms.css', 'resources/js/gptfms.js'])
    @stack('styles')
</head>
<body>

<!-- ═══════════════ TOAST CONTAINER ═══════════════ -->
<div id="toast-container"></div>

<!-- ═══════════════ NOTIFICATION DROPDOWN ═══════════════ -->
<div id="notif-dropdown" class="dropdown">
    <div class="dropdown-header"><i class="uil uil-bell me-2"></i> Notifications <span style="color:var(--text-muted);font-weight:400;font-size:11px;float:right">Mark all read</span></div>
    <div class="notif-item"><div class="notif-text"><i class="uil uil-clipboard-notes me-2"></i> <strong>Task assigned:</strong> Design system review</div><div class="notif-time">2 min ago</div></div>
    <div class="notif-item"><div class="notif-text"><i class="uil uil-users-alt me-2"></i> <strong>Group Alpha</strong> added you as leader</div><div class="notif-time">18 min ago</div></div>
</div>

<!-- ═══════════════ USER PROFILE DROPDOWN ═══════════════ -->
<div id="user-dropdown" class="dropdown" style="width: 220px; right: 20px;">
    @auth
    <div class="dropdown-header" style="padding: 15px; border-bottom: 1px solid var(--border);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="sidebar-avatar" style="width: 40px; height: 40px; background: {{ auth()->user()->avatar ? 'url('.asset(auth()->user()->avatar).') center/cover' : 'var(--primary)' }}; display: flex; align-items: center; justify-content: center; color: #fff;">
                {{ auth()->user()->avatar ? '' : auth()->user()->initials }}
            </div>
            <div>
                <div class="sidebar-user-name" style="font-weight: 700; font-size: 14px; color: var(--text);">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role" style="font-size: 11px; color: var(--text-muted);">{{ auth()->user()->roles->first()->name ?? 'User' }}</div>
            </div>
        </div>
    </div>
    <div style="padding: 5px 0;">
        <a href="{{ route('profile') }}" class="notif-item" style="display: flex; align-items: center; padding: 10px 15px; text-decoration: none; color: var(--text); font-size: 13px;">
            <i class="uil uil-user me-2" style="font-size: 16px;"></i> My Profile
        </a>
        <a href="{{ route('settings') }}" class="notif-item" style="display: flex; align-items: center; padding: 10px 15px; text-decoration: none; color: var(--text); font-size: 13px;">
            <i class="uil uil-setting me-2" style="font-size: 16px;"></i> Account Settings
        </a>
        <div style="border-top: 1px solid var(--border); margin: 5px 0;"></div>
        <div class="notif-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="display: flex; align-items: center; padding: 10px 15px; cursor: pointer; color: var(--danger); font-size: 13px;">
            <i class="uil uil-sign-out-alt me-2" style="font-size: 16px;"></i> Sign Out
        </div>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    @else
    <div class="dropdown-header" style="padding: 15px; text-align: center;">
        <div class="sidebar-user-name" style="font-weight: 700; font-size: 14px; color: var(--text); margin-bottom: 10px;">Guest User</div>
        <a href="{{ route('login') }}" class="btn btn-primary btn-sm" style="width: 100%;">Sign In</a>
    </div>
    @endauth
</div>

<!-- ═══════════════ CREATE GROUP MODAL ═══════════════ -->
<div class="modal-overlay" id="modal-group">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">Create New Group</span>
            <span class="modal-close" onclick="closeModal('modal-group')"><i class="uil uil-multiply"></i></span>
        </div>
        <form id="createGroupForm">
            @csrf
            <div class="form-group">
                <label class="form-label">Group Name</label>
                <input name="name" class="form-control" placeholder="e.g. Team Alpha" required />
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Course / Project</label>
                    <select name="project_id" class="form-control" required>
                        @php $projects = \App\Models\Project::all(); @endphp
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->course_code }} – {{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Max Members</label>
                    <input name="max_members" class="form-control" type="number" value="5" min="2" max="10" required />
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Group focus, goals..."></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:15px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-group')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Group</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('createGroupForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch("{{ route('groups.store') }}", {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toast(data.message, '<i class="uil uil-check"></i>');
            closeModal('modal-group');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            toast(data.message || 'Error creating group', '<i class="uil uil-exclamation-triangle"></i>');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toast('An error occurred. Please try again.', '<i class="uil uil-exclamation-triangle"></i>');
    });
});
</script>

<!-- ═══════════════ APP SHELL ═══════════════ -->
<div id="app">

    <!-- SIDEBAR -->
    <nav id="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon"><i class="uil uil-bullseye" style="color: #fff;"></i></div>
            <div class="sidebar-logo-text">GPT<span>FMS</span></div>
        </div>

        <div class="sidebar-nav">
            <div class="sidebar-section-label">Overview</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-chart-pie-alt"></i></span><span class="nav-label">Dashboard</span>
            </a>
            @role('student')
            <a href="{{ route('groups') }}" class="nav-item {{ request()->routeIs('groups') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-users-alt"></i></span><span class="nav-label">My Group</span>
            </a>
            <a href="{{ route('survey.index') }}" class="nav-item {{ request()->routeIs('survey.index') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-clipboard-notes"></i></span><span class="nav-label">Skills Survey</span>
            </a>
            @endrole

            @role('supervisor')
            <a href="{{ route('supervisor') }}" class="nav-item {{ request()->routeIs('supervisor') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-graduation-cap"></i></span><span class="nav-label">Supervisor Hub</span>
            </a>
            @endrole

            <div class="sidebar-section-label">Work</div>
            <a href="{{ route('groups.settings') }}" class="nav-item {{ request()->routeIs('groups.settings') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-users-alt"></i></span><span class="nav-label">Group Settings</span>
            </a>
            <a href="{{ route('projects') }}" class="nav-item {{ request()->routeIs('projects') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-folder"></i></span><span class="nav-label">Projects</span>
            </a>
            <a href="{{ route('tasks') }}" class="nav-item {{ request()->routeIs('tasks') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-check-circle"></i></span><span class="nav-label">Tasks</span>
            </a>
            <a href="{{ route('messages') }}" class="nav-item {{ request()->routeIs('messages') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-comment-dots"></i></span><span class="nav-label">Messages</span>
            </a>

            <div class="sidebar-section-label">Insights</div>
            <a href="{{ route('reports') }}" class="nav-item {{ request()->routeIs('reports') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-analytics"></i></span><span class="nav-label">Analytics</span>
            </a>
            <a href="{{ route('evaluation') }}" class="nav-item {{ request()->routeIs('evaluation') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-star"></i></span><span class="nav-label">Peer Eval</span>
            </a>

            @role('admin')
            <div class="sidebar-section-label">Administration</div>
            <a href="{{ route('admin') }}" class="nav-item {{ request()->routeIs('admin') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-wrench"></i></span><span class="nav-label">Admin Control</span>
            </a>
            <a href="{{ route('users') }}" class="nav-item {{ request()->routeIs('users') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-user-circle"></i></span><span class="nav-label">User Management</span>
            </a>
            @endrole

            <div class="sidebar-section-label">System</div>
            <a href="{{ route('settings') }}" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-setting"></i></span><span class="nav-label">Settings</span>
            </a>
            <a href="{{ route('profile') }}" class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-user"></i></span><span class="nav-label">Profile</span>
            </a>
            @auth
            <a href="#" class="nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="nav-icon"><i class="uil uil-sign-out-alt"></i></span><span class="nav-label">Logout</span>
            </a>
            @endauth
        </div>

        <div class="sidebar-footer">
            @auth
            <div class="sidebar-user" onclick="toggleDropdown('user-dropdown')">
                <div class="sidebar-avatar" style="background: {{ auth()->user()->avatar ? 'url('.asset(auth()->user()->avatar).') center/cover' : 'var(--primary)' }}; display: flex; align-items: center; justify-content: center; color: #fff;">
                    {{ auth()->user()->avatar ? '' : auth()->user()->initials }}
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">{{ auth()->user()->roles->first()->name ?? 'User' }}</div>
                </div>
            </div>
            @else
            <a href="{{ route('login') }}" class="nav-item">
                <span class="nav-icon"><i class="uil uil-sign-in-alt"></i></span><span class="nav-label">Sign In</span>
            </a>
            @endauth
        </div>
    </nav>

    <!-- MAIN -->
    <div id="main">

        <!-- NAVBAR -->
        <nav id="navbar">
            <button class="navbar-toggle" onclick="toggleSidebar()" title="Toggle sidebar"><i class="uil uil-bars"></i></button>
            <div class="navbar-search">
                <span><i class="uil uil-search"></i></span>
                <input placeholder="Search groups, tasks, members…" />
            </div>
            <div style="margin-left:8px;">
                <span id="page-breadcrumb" class="page-breadcrumb">@yield('breadcrumb', 'Dashboard')</span>
            </div>
            <div class="navbar-right">
                <button class="navbar-btn" onclick="toast('Synced with server','<i class=\'uil uil-sync\'></i>')"><i class="uil uil-sync"></i></button>
                <div style="position:relative">
                    <button class="navbar-btn" onclick="toggleDropdown('notif-dropdown')" id="notif-btn"><span class="dot"></span><i class="uil uil-bell"></i></button>
                </div>
                <a href="{{ route('settings') }}" class="navbar-btn"><i class="uil uil-setting"></i></a>
                @auth
                <div class="navbar-avatar" onclick="toggleDropdown('user-dropdown')" style="background: {{ auth()->user()->avatar ? 'url('.asset(auth()->user()->avatar).') center/cover' : 'var(--primary)' }}; display: flex; align-items: center; justify-content: center; color: #fff;">
                    {{ auth()->user()->avatar ? '' : auth()->user()->initials }}
                </div>
                @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Login</a>
                @endauth
            </div>
        </nav>

        <!-- CONTENT -->
        <div id="content">
            @yield('content')
        </div><!-- /content -->
    </div><!-- /main -->
</div><!-- /app -->

@stack('scripts')
</body>
</html>
