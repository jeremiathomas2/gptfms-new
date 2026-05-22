@extends('layouts.app')

@section('breadcrumb', 'Settings')

@section('content')
@php
    $user = auth()->user();
@endphp
<div class="page active" id="page-settings">
    @if(!$user)
        <div class="card" style="padding:18px">
            <div style="font-weight:800;margin-bottom:6px">Session expired</div>
            <div style="color:var(--text-muted);margin-bottom:12px">Please sign in again to access settings.</div>
            <a class="btn btn-primary btn-sm" href="{{ route('login') }}"><i class="uil uil-sign-in-alt me-1"></i> Login</a>
        </div>
    @else
    <div class="section-header">
        <div><div class="section-title">Settings</div><div class="section-sub">Customize your GPTFMS experience</div></div>
        <button class="btn btn-primary btn-sm" onclick="saveActiveSettings()"><i class="uil uil-save me-1"></i> Save Changes</button>
    </div>
    <div class="settings-grid" style="min-height:500px">
        <div class="settings-nav">
            <div class="settings-nav-item active" onclick="switchSettings('appearance',this)"><i class="uil uil-palette me-2"></i> Appearance</div>
            <div class="settings-nav-item" onclick="switchSettings('profile',this)"><i class="uil uil-user me-2"></i> Profile</div>
            <div class="settings-nav-item" onclick="switchSettings('notifications',this)"><i class="uil uil-bell me-2"></i> Notifications</div>
            <div class="settings-nav-item" onclick="switchSettings('security',this)"><i class="uil uil-lock me-2"></i> Security</div>
        </div>
        <div class="settings-body">

            <!-- Appearance -->
            <div class="settings-section active" id="settings-appearance">
                <div style="font-size:15px;font-weight:700;margin-bottom:18px">Appearance & Theme</div>

                <div style="margin-bottom:20px">
                    <div class="form-label" style="margin-bottom:10px">Color Theme</div>
                    <div style="display:flex;gap:12px">
                        <div onclick="setTheme('light')" style="flex:1;padding:14px;border-radius:10px;border:2px solid var(--primary);background:#F9FAFB;cursor:pointer;text-align:center">
                            <div style="font-size:22px"><i class="uil uil-sun"></i></div><div style="font-size:12px;font-weight:600;color:#111;margin-top:5px">Light</div>
                        </div>
                        <div onclick="setTheme('dark')" style="flex:1;padding:14px;border-radius:10px;border:2px solid var(--border);background:#1E293B;cursor:pointer;text-align:center">
                            <div style="font-size:22px"><i class="uil uil-moon"></i></div><div style="font-size:12px;font-weight:600;color:#fff;margin-top:5px">Dark</div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom:20px">
                    <div class="form-label" style="margin-bottom:10px">Sidebar Color</div>
                    <div class="color-swatches" id="sidebar-swatches">
                        <div class="swatch selected" style="background:#1E293B" onclick="setSidebarColor('#1E293B','#CBD5E1',this)" title="Slate"></div>
                        <div class="swatch" style="background:#1a1a2e" onclick="setSidebarColor('#1a1a2e','#a8b3cf',this)" title="Navy"></div>
                        <div class="swatch" style="background:#14532d" onclick="setSidebarColor('#14532d','#bbf7d0',this)" title="Forest"></div>
                        <div class="swatch" style="background:#1e1b4b" onclick="setSidebarColor('#1e1b4b','#c7d2fe',this)" title="Violet"></div>
                        <div class="swatch" style="background:#431407" onclick="setSidebarColor('#431407','#fed7aa',this)" title="Burnt"></div>
                        <div class="swatch" style="background:#0c0a09" onclick="setSidebarColor('#0c0a09','#d6d3d1',this)" title="Black"></div>
                        <div class="swatch" style="background:#1c1917" onclick="setSidebarColor('#1c1917','#d6d3d1',this)" title="Charcoal"></div>
                        <div class="swatch" style="background:#164e63" onclick="setSidebarColor('#164e63','#a5f3fc',this)" title="Ocean"></div>
                    </div>
                </div>

                <div style="margin-bottom:20px">
                    <div class="form-label" style="margin-bottom:10px">Header Color</div>
                    <div class="color-swatches" id="header-swatches">
                        <div class="swatch selected" style="background:#ffffff;border:1px solid #e5e7eb" onclick="setHeaderColor('#ffffff','#111827',this)" title="White"></div>
                        <div class="swatch" style="background:#F0F9FF;border:1px solid #bae6fd" onclick="setHeaderColor('#F0F9FF','#0c4a6e',this)" title="Sky"></div>
                        <div class="swatch" style="background:#F0FDF4;border:1px solid #bbf7d0" onclick="setHeaderColor('#F0FDF4','#14532d',this)" title="Mint"></div>
                        <div class="swatch" style="background:#2563EB" onclick="setHeaderColor('#2563EB','#fff',this)" title="Blue"></div>
                        <div class="swatch" style="background:#10B981" onclick="setHeaderColor('#10B981','#fff',this)" title="Green"></div>
                        <div class="swatch" style="background:#1E293B" onclick="setHeaderColor('#1E293B','#F1F5F9',this)" title="Dark"></div>
                        <div class="swatch" style="background:#7C3AED" onclick="setHeaderColor('#7C3AED','#fff',this)" title="Purple"></div>
                        <div class="swatch" style="background:#DC2626" onclick="setHeaderColor('#DC2626','#fff',this)" title="Red"></div>
                    </div>
                </div>

                <div style="margin-bottom:20px">
                    <div class="form-label">Accent / Primary Color</div>
                    <div class="color-swatches" style="margin-top:8px" id="accent-swatches">
                        <div class="swatch selected" style="background:#2563EB" onclick="setAccentColor('#2563EB','#DBEAFE',this)"></div>
                        <div class="swatch" style="background:#10B981" onclick="setAccentColor('#10B981','#D1FAE5',this)"></div>
                        <div class="swatch" style="background:#8B5CF6" onclick="setAccentColor('#8B5CF6','#EDE9FE',this)"></div>
                        <div class="swatch" style="background:#F59E0B" onclick="setAccentColor('#F59E0B','#FEF3C7',this)"></div>
                        <div class="swatch" style="background:#EF4444" onclick="setAccentColor('#EF4444','#FEE2E2',this)"></div>
                        <div class="swatch" style="background:#06B6D4" onclick="setAccentColor('#06B6D4','#CFFAFE',this)"></div>
                        <div class="swatch" style="background:#EC4899" onclick="setAccentColor('#EC4899','#FCE7F3',this)"></div>
                        <div class="swatch" style="background:#0EA5E9" onclick="setAccentColor('#0EA5E9','#E0F2FE',this)"></div>
                    </div>
                </div>

                <div style="margin-bottom:20px">
                    <div class="form-label">Sidebar Width</div>
                    <div style="display:flex;align-items:center;gap:12px;margin-top:8px">
                        <span style="font-size:12px;color:var(--text-muted)">Narrow</span>
                        <input type="range" min="200" max="300" value="240" style="flex:1" oninput="document.documentElement.style.setProperty('--sidebar-width', this.value+'px')"/>
                        <span style="font-size:12px;color:var(--text-muted)">Wide</span>
                    </div>
                </div>

                <div style="margin-bottom:20px">
                    <div class="form-label">Hover Scale Effect</div>
                    <div style="display:flex;align-items:center;gap:12px;margin-top:8px">
                        <span style="font-size:12px;color:var(--text-muted)">None</span>
                        <input type="range" min="1" max="1.05" step="0.005" value="1.025" style="flex:1" oninput="document.documentElement.style.setProperty('--hover-scale', this.value)"/>
                        <span style="font-size:12px;color:var(--text-muted)">Max</span>
                    </div>
                </div>

                <div>
                    <div class="form-label">Transition Speed</div>
                    <div style="display:flex;align-items:center;gap:12px;margin-top:8px">
                        <span style="font-size:12px;color:var(--text-muted)">Instant</span>
                        <input type="range" min="50" max="600" step="50" value="220" style="flex:1" oninput="setTransitionSpeed(this.value)"/>
                        <span style="font-size:12px;color:var(--text-muted)">Slow</span>
                    </div>
                </div>

                <div style="margin-top:20px">
                    <div class="form-label">Border Radius</div>
                    <div style="display:flex;align-items:center;gap:12px;margin-top:8px">
                        <span style="font-size:12px;color:var(--text-muted)">Square</span>
                        <input type="range" min="0" max="20" value="12" style="flex:1" oninput="document.documentElement.style.setProperty('--radius', this.value+'px')"/>
                        <span style="font-size:12px;color:var(--text-muted)">Pill</span>
                    </div>
                </div>
            </div>

            <!-- Profile -->
            <div class="settings-section" id="settings-profile">
                <div style="font-size:15px;font-weight:700;margin-bottom:18px">Profile Settings</div>
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:22px">
                    <div id="profile-avatar-container" style="width:64px;height:64px;border-radius:16px;background:{{ $user->avatar ? 'url('.asset($user->avatar).') center/cover' : 'linear-gradient(135deg,var(--primary),var(--secondary))' }};display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#fff">
                        {{ $user->avatar ? '' : $user->initials }}
                    </div>
                    <div>
                        <input type="file" id="profile-avatar-input" style="display: none;" accept="image/*" onchange="previewAvatar(this)">
                        <button class="btn btn-outline btn-sm" onclick="document.getElementById('profile-avatar-input').click()">
                            <i class="uil uil-camera me-1"></i> Change Photo
                        </button>
                        <div style="font-size:11.5px;color:var(--text-muted);margin-top:5px">JPG or PNG, max 2MB</div>
                    </div>
                </div>
                <form id="profile-form" onsubmit="event.preventDefault(); saveProfile();">
                    @csrf
                    <div class="form-row-3">
                        <div class="form-group"><label class="form-label">First Name</label><input name="first_name" class="form-control" id="profile-first-name" value="{{ $user->first_name }}"/></div>
                        <div class="form-group"><label class="form-label">Middle Name</label><input name="middle_name" class="form-control" id="profile-middle-name" value="{{ $user->middle_name }}"/></div>
                        <div class="form-group"><label class="form-label">Last Name</label><input name="last_name" class="form-control" id="profile-last-name" value="{{ $user->last_name }}"/></div>
                    </div>
                    <div class="form-group"><label class="form-label">Email</label><input name="email" class="form-control" id="profile-email" type="email" value="{{ $user->email }}"/></div>
                    <div class="form-group"><label class="form-label">Registration Number</label><input class="form-control" value="{{ $user->registration_number ?? 'N/A' }}" readonly/></div>
                    <div class="form-group"><label class="form-label">Skills (comma-separated)</label><input name="skills" class="form-control" id="profile-skills" value="{{ $user->skills ?? 'React, Node.js, PostgreSQL, Docker' }}"/></div>
                    <div class="form-group"><label class="form-label">Bio</label><textarea name="bio" class="form-control" id="profile-bio" rows="3">{{ $user->bio ?? 'No bio set.' }}</textarea></div>
                    <button type="submit" class="btn btn-primary"><i class="uil uil-check me-1"></i> Save Profile</button>
                </form>
            </div>

            <!-- Notifications -->
            <div class="settings-section" id="settings-notifications">
                <div style="font-size:15px;font-weight:700;margin-bottom:18px">Notification Preferences</div>
                <div class="toggle-row"><div><div class="toggle-label">Task Assignments</div><div class="toggle-desc">When a task is assigned to you</div></div><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
                <div class="toggle-row"><div><div class="toggle-label">Milestone Reminders</div><div class="toggle-desc">24 hours before deadlines</div></div><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
                <div class="toggle-row"><div><div class="toggle-label">Group Messages</div><div class="toggle-desc">New messages in group chats</div></div><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
                <div class="toggle-row"><div><div class="toggle-label">Peer Evaluations</div><div class="toggle-desc">Evaluation submission reminders</div></div><div class="toggle" onclick="this.classList.toggle('on')"></div></div>
                <div class="toggle-row"><div><div class="toggle-label">System Announcements</div><div class="toggle-desc">Platform updates and maintenance</div></div><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
                <div class="toggle-row"><div><div class="toggle-label">Email Digest</div><div class="toggle-desc">Daily summary via email</div></div><div class="toggle" onclick="this.classList.toggle('on')"></div></div>
            </div>

            <!-- Security -->
            <div class="settings-section" id="settings-security">
                <div style="font-size:15px;font-weight:700;margin-bottom:18px">Security Settings</div>
                <form id="security-form" onsubmit="event.preventDefault(); updateSecurity();">
                    @csrf
                    <div class="form-group"><label class="form-label">Current Password</label><input name="current_password" class="form-control" id="security-current-password" type="password" placeholder="Enter current password"/></div>
                    <div class="form-group"><label class="form-label">New Password</label><input name="password" class="form-control" id="security-new-password" type="password" placeholder="Enter new password"/></div>
                    <div class="form-group"><label class="form-label">Confirm New Password</label><input name="password_confirmation" class="form-control" id="security-confirm-password" type="password" placeholder="Confirm new password"/></div>
                    <button type="submit" class="btn btn-primary" style="margin-bottom:22px">Update Password</button>
                </form>
                <div style="border-top:1px solid var(--border);padding-top:18px">
                    <div style="font-size:14px;font-weight:700;margin-bottom:12px">Two-Factor Authentication</div>
                    <div class="toggle-row"><div><div class="toggle-label">Enable 2FA</div><div class="toggle-desc">Adds an extra layer of security</div></div><div class="toggle" onclick="this.classList.toggle('on');toast('2FA toggled!','🔐')"></div></div>
                </div>
                <div style="border-top:1px solid var(--border);padding-top:18px;margin-top:4px">
                    <div style="font-size:14px;font-weight:700;margin-bottom:12px;color:var(--danger)">Danger Zone</div>
                    <button class="btn btn-danger btn-sm" onclick="toast('Account deletion requires confirmation','⚠️')">Delete Account</button>
                </div>
            </div>

        </div>
    </div>
</div>
    @endif
@endsection

@push('scripts')
<script>
function saveActiveSettings() {
    const activeSection = document.querySelector('.settings-section.active');
    if (!activeSection) return;
    
    const id = activeSection.id;
    if (id === 'settings-profile') {
        saveProfile();
    } else if (id === 'settings-security') {
        updateSecurity();
    } else {
        toast('Settings saved successfully', '<i class="uil uil-check-circle"></i>');
    }
}

function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('profile-avatar-container');
            container.style.background = `url(${e.target.result}) center/cover`;
            container.innerHTML = '';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function saveProfile(event) {
    if (event) event.preventDefault();
    
    const form = document.getElementById('profile-form');
    const formData = new FormData(form);
    const avatarInput = document.getElementById('profile-avatar-input');
    if (avatarInput && avatarInput.files && avatarInput.files[0]) {
        formData.append('avatar', avatarInput.files[0]);
    }
    
    toast('Saving profile...', '<i class="uil uil-spinner-alt uil-spin"></i>');
    
    fetch("{{ route('settings.profile') }}", {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        toast(data.message, '<i class="uil uil-check-circle"></i>');
        if (data && data.avatar_url && window.updateUserAvatar) {
            window.updateUserAvatar(data.avatar_url, data.user ? data.user.initials : null);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toast('Failed to save profile', '<i class="uil uil-exclamation-triangle"></i>');
    });
}

function updateSecurity(event) {
    if (event) event.preventDefault();
    
    const form = document.getElementById('security-form');
    const formData = new FormData(form);
    
    toast('Updating password...', '<i class="uil uil-spinner-alt uil-spin"></i>');
    
    fetch("{{ route('settings.security') }}", {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.errors) {
            toast(Object.values(data.errors)[0][0], '<i class="uil uil-exclamation-triangle"></i>');
        } else {
            toast(data.message, '<i class="uil uil-check-circle"></i>');
            form.reset();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toast('Failed to update password', '<i class="uil uil-exclamation-triangle"></i>');
    });
}
</script>
@endpush
