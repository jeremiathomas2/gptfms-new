<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GPTFMS') }} — Group Project Team Formation & Management</title>

    <!-- Pre-render Theme Script -->
    <script>
        (function() {
            const theme = localStorage.getItem('gptfms-theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
            
            const sidebarBg = localStorage.getItem('gptfms-sidebar-bg');
            const sidebarText = localStorage.getItem('gptfms-sidebar-text');
            if (sidebarBg && sidebarText) {
                document.documentElement.style.setProperty('--sidebar-bg', sidebarBg);
                document.documentElement.style.setProperty('--sidebar-text', sidebarText);
            }

            const headerBg = localStorage.getItem('gptfms-header-bg');
            const headerText = localStorage.getItem('gptfms-header-text');
            if (headerBg && headerText) {
                document.documentElement.style.setProperty('--header-bg', headerBg);
                document.documentElement.style.setProperty('--header-text', headerText);
            }

            const accentColor = localStorage.getItem('gptfms-accent-color');
            const accentLight = localStorage.getItem('gptfms-accent-light');
            if (accentColor && accentLight) {
                document.documentElement.style.setProperty('--primary', accentColor);
                document.documentElement.style.setProperty('--primary-light', accentLight);
                document.documentElement.style.setProperty('--sidebar-active', accentColor);
            }

            const transitionSpeed = localStorage.getItem('gptfms-transition-speed');
            if (transitionSpeed) {
                document.documentElement.style.setProperty('--transition', `${transitionSpeed}ms cubic-bezier(.4,0,.2,1)`);
            }

            const sidebarCollapsed = localStorage.getItem('gptfms-sidebar-collapsed') === 'true';
            if (sidebarCollapsed) {
                // We'll apply the class later since the body isn't ready yet,
                // but we can set a data attribute or global variable.
                window.__SIDEBAR_COLLAPSED__ = true;
            }
        })();
    </script>
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
<body class="preload">

<!-- ═══════════════ TOAST CONTAINER ═══════════════ -->
<div id="toast-container"></div>

<!-- ═══════════════ NOTIFICATION DROPDOWN ═══════════════ -->
<div id="notif-dropdown" class="dropdown">
    <div class="dropdown-header">
        <i class="uil uil-bell me-2"></i> Notifications 
        <span id="mark-all-read" style="color:var(--text-muted);font-weight:400;font-size:11px;float:right;cursor:pointer">Mark all read</span>
    </div>
    <div id="notif-items-container" style="max-height: 300px; overflow-y: auto;">
        <!-- Notifications will be loaded here via JS -->
        <div class="notif-item" style="text-align: center; padding: 20px;">
            <div class="notif-text">Loading notifications...</div>
        </div>
    </div>
</div>

<!-- ═══════════════ USER PROFILE DROPDOWN ═══════════════ -->
<div id="user-dropdown" class="dropdown" style="width: 220px; right: 20px;">
    @auth
    <div class="dropdown-header" style="padding: 15px; border-bottom: 1px solid var(--border);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div id="dropdown-user-avatar" class="sidebar-avatar" style="width: 40px; height: 40px; background: {{ auth()->user()->avatar ? 'url('.asset(auth()->user()->avatar).') center/cover' : 'var(--primary)' }}; display: flex; align-items: center; justify-content: center; color: #fff;">
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
        <script>
            // Apply collapsed state immediately to prevent width "blink"
            if (localStorage.getItem('gptfms-sidebar-collapsed') === 'true') {
                document.getElementById('sidebar').classList.add('collapsed');
            }
        </script>
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
            <a href="{{ route('my_group') }}" class="nav-item {{ request()->routeIs('my_group') ? 'active' : '' }}">
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
            <a href="{{ route('my_group') }}" class="nav-item {{ request()->routeIs('my_group') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-users-alt"></i></span><span class="nav-label">My Groups</span>
            </a>
            @endrole

            <div class="sidebar-section-label">Work</div>
            @role('admin')
            <a href="{{ route('groups.settings') }}" class="nav-item {{ request()->routeIs('groups.settings') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-users-alt"></i></span><span class="nav-label">Group Settings</span>
            </a>
            @endrole
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
            <a href="{{ route('admin.groups') }}" class="nav-item {{ request()->routeIs('admin.groups') ? 'active' : '' }}">
                <span class="nav-icon"><i class="uil uil-users-alt"></i></span><span class="nav-label">Group Management</span>
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
        <script>
            // Apply scroll position immediately to prevent vertical "blink"
            (function() {
                const nav = document.querySelector('.sidebar-nav');
                const pos = localStorage.getItem('gptfms-sidebar-scroll');
                if (nav && pos) {
                    nav.scrollTop = parseInt(pos, 10);
                }
            })();
        </script>

        <div class="sidebar-footer">
            @auth
            <div class="sidebar-user" onclick="toggleDropdown('user-dropdown')">
                <div id="sidebar-footer-avatar" class="sidebar-avatar" style="background: {{ auth()->user()->avatar ? 'url('.asset(auth()->user()->avatar).') center/cover' : 'var(--primary)' }}; display: flex; align-items: center; justify-content: center; color: #fff;">
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
                <button id="sync-btn" class="navbar-btn" onclick="syncWithServer()" title="Sync with server">
                    <i class="uil uil-sync"></i>
                </button>
                <div style="position:relative">
                    <button class="navbar-btn" onclick="toggleDropdown('notif-dropdown')" id="notif-btn">
                        <span id="notif-dot" class="dot" style="display:none"></span>
                        <i class="uil uil-bell"></i>
                    </button>
                </div>
                <a href="{{ route('settings') }}" class="navbar-btn"><i class="uil uil-setting"></i></a>
                @auth
                <div id="navbar-user-avatar" class="navbar-avatar" onclick="toggleDropdown('user-dropdown')" style="background: {{ auth()->user()->avatar ? 'url('.asset(auth()->user()->avatar).') center/cover' : 'var(--primary)' }}; display: flex; align-items: center; justify-content: center; color: #fff;">
                    {{ auth()->user()->avatar ? '' : auth()->user()->initials }}
                </div>
                @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Login</a>
                @endauth
            </div>
        </nav>

        <div id="idle-warning" class="idle-warning" style="display:none" aria-hidden="true">
            <div class="idle-warning-inner">
                <div class="idle-warning-left">
                    <i class="uil uil-clock-three"></i>
                    <div>
                        <div class="idle-warning-title">You will be signed out soon</div>
                        <div class="idle-warning-sub">No activity detected. Auto logout in <span id="idle-warning-countdown">01:00</span>.</div>
                    </div>
                </div>
                <div class="idle-warning-actions">
                    <button type="button" class="btn btn-primary btn-sm" id="idle-warning-stay">Stay signed in</button>
                </div>
            </div>
        </div>

        <!-- CONTENT -->
        <div id="content">
            @yield('content')
        </div><!-- /content -->
    </div><!-- /main -->
</div><!-- /app -->

@stack('scripts')
</body>
</html>
