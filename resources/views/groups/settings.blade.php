@extends('layouts.app')

@section('breadcrumb', 'Group Settings')

@section('content')
<div class="page active" id="page-group-settings">
    <div class="section-header" style="margin-bottom: 30px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);">
                <i class="uil uil-users-alt" style="font-size: 24px;"></i>
            </div>
            <div>
                <div class="section-title" style="font-size: 22px; letter-spacing: -0.5px;">Group Formation Settings</div>
                <div class="section-sub" style="font-size: 13px;">Manage automated team creation and lifecycle parameters</div>
            </div>
        </div>
        <div style="display:flex; gap:12px;">
            <button type="button" class="btn btn-outline" style="padding: 8px 16px; border-radius: 10px; font-weight: 600;" onclick="toast('Defaults restored','<i class=\'uil uil-history\'></i>')">
                <i class="uil uil-history me-1"></i> Reset
            </button>
            <button type="submit" form="group-settings-form" class="btn btn-primary" style="padding: 8px 20px; border-radius: 10px; font-weight: 700; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);">
                <i class="uil uil-save me-1"></i> Save Changes
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="card" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); color: #065F46; margin-bottom: 28px; padding: 14px 20px; border-radius: 14px; display: flex; align-items: center; animation: slideIn 0.3s ease-out;">
            <i class="uil uil-check-circle me-3" style="font-size: 20px;"></i> 
            <span style="font-weight: 600; font-size: 14px;">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid-2" style="gap: 28px; align-items: start;">
        <!-- Left Column: Configuration -->
        <div class="card" style="padding: 28px; border-radius: 18px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 30px; padding-bottom: 18px; border-bottom: 1px solid var(--border);">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(37, 99, 235, 0.08); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                    <i class="uil uil-sliders-v-alt" style="font-size: 22px;"></i>
                </div>
                <div>
                    <h3 style="font-size: 17px; font-weight: 700; margin: 0; color: var(--text);">Configuration Panel</h3>
                    <p style="font-size: 12px; color: var(--text-muted); margin: 2px 0 0 0;">Adjust algorithm parameters</p>
                </div>
            </div>

            <form action="{{ route('groups.settings.update') }}" method="POST" id="group-settings-form">
                @csrf
                <div style="display: grid; gap: 24px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight: 700; color: var(--text); margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                            <i class="uil uil-users-alt" style="color: var(--primary); font-size: 16px;"></i> Participants Per Group
                        </label>
                        <div style="position: relative;">
                            <input type="number" name="participants_per_group" class="form-control" value="{{ $settings->participants_per_group ?? 5 }}" min="1" 
                                style="padding: 12px 16px; border-radius: 10px; font-weight: 600; background: var(--bg-alt); border: 1px solid var(--border); width: 100%;">
                        </div>
                        <p style="color: var(--text-muted); font-size: 11.5px; margin-top: 8px; line-height: 1.4;">Ideal size for each team during auto-formation.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-weight: 700; color: var(--text); margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                            <i class="uil uil-stopwatch" style="color: var(--primary); font-size: 16px;"></i> Formation Deadline (Minutes)
                        </label>
                        <input type="number" name="countdown_minutes" class="form-control" value="{{ $settings->countdown_minutes ?? 60 }}" min="0"
                            style="padding: 12px 16px; border-radius: 10px; font-weight: 600; background: var(--bg-alt); border: 1px solid var(--border); width: 100%;">
                        <p style="color: var(--text-muted); font-size: 11.5px; margin-top: 8px; line-height: 1.4;">Window for manual student team-up before auto-run.</p>
                    </div>

                    <div style="margin-top: 10px; padding: 22px; background: var(--bg-alt); border-radius: 16px; border: 1px solid var(--border);">
                        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; color: var(--text);">
                            <i class="uil uil-shield-check me-2" style="color: var(--primary); font-size: 18px;"></i> Smart Formation Rules
                        </h4>
                        
                        <div style="display: grid; gap: 18px;">
                            <label class="rule-option" style="display: flex; align-items: flex-start; cursor: pointer; transition: all 0.2s;">
                                <div class="checkbox-wrapper" style="margin-top: 2px;">
                                    <input type="checkbox" name="auto_create_groups" value="1" {{ ($settings->auto_create_groups ?? false) ? 'checked' : '' }} 
                                        style="width: 20px; height: 20px; border-radius: 6px; accent-color: var(--primary);">
                                </div>
                                <div style="margin-left: 14px;">
                                    <span style="font-size: 13.5px; font-weight: 700; color: var(--text); display: block;">Automated Lifecycle</span>
                                    <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px;">System creates groups instantly on deadline.</span>
                                </div>
                            </label>

                            <label class="rule-option" style="display: flex; align-items: flex-start; cursor: pointer;">
                                <div class="checkbox-wrapper" style="margin-top: 2px;">
                                    <input type="checkbox" name="balance_by_gender" value="1" {{ ($settings->balance_by_gender ?? false) ? 'checked' : '' }}
                                        style="width: 20px; height: 20px; border-radius: 6px; accent-color: var(--primary);">
                                </div>
                                <div style="margin-left: 14px;">
                                    <span style="font-size: 13.5px; font-weight: 700; color: var(--text); display: block;">Gender Distribution</span>
                                    <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px;">Ensures diverse team representation.</span>
                                </div>
                            </label>

                            <label class="rule-option" style="display: flex; align-items: flex-start; cursor: pointer;">
                                <div class="checkbox-wrapper" style="margin-top: 2px;">
                                    <input type="checkbox" name="balance_by_skills" value="1" {{ ($settings->balance_by_skills ?? false) ? 'checked' : '' }}
                                        style="width: 20px; height: 20px; border-radius: 6px; accent-color: var(--primary);">
                                </div>
                                <div style="margin-left: 14px;">
                                    <span style="font-size: 13.5px; font-weight: 700; color: var(--text); display: block;">Skill-Based Pairing</span>
                                    <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px;">Uses survey data for technical balance.</span>
                                </div>
                            </label>

                            <div style="margin-top: 6px; padding: 14px; background: rgba(37, 99, 235, 0.04); border-radius: 12px; border: 1px dashed rgba(37, 99, 235, 0.3);">
                                <label style="display: flex; align-items: flex-start; cursor: pointer;">
                                    <input type="checkbox" name="is_active" value="1" {{ ($settings->is_active ?? false) ? 'checked' : '' }}
                                        style="width: 20px; height: 20px; border-radius: 6px; accent-color: var(--primary); margin-top: 2px;">
                                    <div style="margin-left: 14px;">
                                        <span style="font-size: 14px; font-weight: 800; color: var(--primary); display: block;">Activate System</span>
                                        <span style="font-size: 11px; color: var(--primary); opacity: 0.8; font-weight: 500;">Enables the countdown timer</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Column: Status -->
        <div style="display: grid; gap: 28px;">
            <div class="card" style="padding: 28px; border-radius: 18px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--border); background: #fff;">
                <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 24px; padding-bottom: 18px; border-bottom: 1px solid var(--border);">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16, 185, 129, 0.08); display: flex; align-items: center; justify-content: center; color: var(--success);">
                        <i class="uil uil-hourglass" style="font-size: 22px;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 17px; font-weight: 700; margin: 0; color: var(--text);">Live Status</h3>
                        <p style="font-size: 12px; color: var(--text-muted); margin: 2px 0 0 0;">Real-time formation monitoring</p>
                    </div>
                </div>
                
                <div style="text-align: center; padding: 45px 0; background: linear-gradient(to bottom, var(--bg-alt), #fff); border-radius: 20px; border: 1px solid var(--border); margin-bottom: 28px; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(37,99,235,0.03); border-radius: 50%;"></div>
                    
                    <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px; font-weight: 800; margin-bottom: 18px;">Time Remaining</div>
                    <div id="countdown-timer" style="font-size: 64px; font-weight: 900; color: var(--text); font-family: 'JetBrains Mono', monospace; letter-spacing: -3px; line-height: 1; text-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        {{ $settings->formatted_remaining_time ?? '00:00:00' }}
                    </div>
                    
                    <div style="margin-top: 35px; display: flex; justify-content: center;">
                        <span id="countdown-status-badge" class="badge {{ $settings->isCountdownRunning() ? 'badge-green' : 'badge-gray' }}" 
                            style="padding: 10px 20px; font-size: 12px; border-radius: 40px; font-weight: 800; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                            <span class="status-dot" style="width: 8px; height: 8px; border-radius: 50%; background: currentColor; animation: {{ $settings->isCountdownRunning() ? 'pulse 1.5s infinite' : 'none' }};"></span>
                            <span id="countdown-status-text">{{ $settings->isCountdownRunning() ? 'SYSTEM ACTIVE' : 'SYSTEM PAUSED' }}</span>
                        </span>
                    </div>
                </div>

                <div style="display: grid; gap: 14px;">
                    <button type="button" class="btn btn-primary" id="start-countdown-btn" 
                        style="height: 54px; border-radius: 14px; font-weight: 700; font-size: 15px; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.2s;" 
                        {{ $settings->isCountdownRunning() ? 'disabled' : '' }} onclick="startCountdown()">
                        <i class="uil uil-play" style="font-size: 20px;"></i> Start Formation
                    </button>
                    
                    <div style="display: flex; gap: 12px;">
                        <button type="button" class="btn btn-outline" style="flex: 1; height: 48px; border-radius: 12px; font-weight: 600;" onclick="toast('System paused','<i class=\'uil uil-pause\'></i>')">
                            <i class="uil uil-pause me-2"></i> Pause
                        </button>
                        <button type="button" class="btn btn-outline" style="flex: 1; height: 48px; border-radius: 12px; font-weight: 600; color: var(--danger); border-color: rgba(220, 38, 38, 0.15); background: rgba(220, 38, 38, 0.02);" onclick="triggerAutoFormation()">
                            <i class="uil uil-bolt-alt me-2"></i> Run Now
                        </button>
                    </div>
                </div>
            </div>

            <div style="padding: 24px; background: #FFFBEB; border-radius: 18px; border: 1px solid #FEF3C7; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.05);">
                <div style="display: flex; gap: 16px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: #FEF3C7; display: flex; align-items: center; justify-content: center; color: #D97706; flex-shrink: 0;">
                        <i class="uil uil-info-circle" style="font-size: 20px;"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 14px; font-weight: 700; color: #92400E; margin: 0 0 6px 0;">Algorithm Notice</h4>
                        <p style="font-size: 12.5px; color: #B45309; margin: 0; line-height: 1.6; opacity: 0.9;">
                            Auto-formation will analyze unassigned students and balance them across teams using your defined criteria.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
        100% { opacity: 1; transform: scale(1); }
    }
    @keyframes slideIn {
        from { transform: translateY(-10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .rule-option:hover {
        transform: translateX(4px);
    }
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endsection

@push('scripts')
<script>
    let countdownInterval = null;
    let endTime = null;

    @if($settings->isCountdownRunning())
        endTime = new Date("{{ $settings->countdown_end_time->toIso8601String() }}");
        startTimer();
    @endif

    function startCountdown() {
        fetch("{{ route('groups.settings.start') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toast(data.message, '<i class="uil uil-play"></i>');
                endTime = new Date(data.end_time);
                document.getElementById('start-countdown-btn').disabled = true;
                
                const badge = document.getElementById('countdown-status-badge');
                badge.className = 'badge badge-green';
                document.getElementById('countdown-status-text').innerText = 'Countdown Active';
                badge.querySelector('i').className = 'uil uil-play me-1';
                
                startTimer();
            }
        });
    }

    function startTimer() {
        if (countdownInterval) clearInterval(countdownInterval);
        
        countdownInterval = setInterval(() => {
            const now = new Date();
            const diff = endTime - now;
            
            if (diff <= 0) {
                clearInterval(countdownInterval);
                document.getElementById('countdown-timer').innerText = '00:00:00';
                triggerAutoFormation();
                return;
            }
            
            const hours = Math.floor(diff / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            
            document.getElementById('countdown-timer').innerText = 
                `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }

    function triggerAutoFormation() {
        if (countdownInterval) clearInterval(countdownInterval);
        
        toast('Triggering auto-formation...', '<i class="uil uil-spinner-alt uil-spin"></i>');
        
        fetch("{{ route('groups.settings.auto_form') }}", {
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
                setTimeout(() => window.location.reload(), 2000);
            } else {
                toast(data.message || 'Formation failed', '<i class="uil uil-exclamation-triangle"></i>');
            }
        });
    }
</script>
@endpush
