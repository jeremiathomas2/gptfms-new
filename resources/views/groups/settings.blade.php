@extends('layouts.app')

@section('breadcrumb', 'Group Settings')

@section('content')
<div class="page active" id="page-group-settings">
    <div class="section-header">
        <div>
            <div class="section-title">Group Formation Settings</div>
            <div class="section-sub">Configure how teams are automatically formed and managed</div>
        </div>
    </div>

    @if(session('success'))
        <div class="card" style="background: var(--success-bg); border-color: var(--success); color: var(--success); margin-bottom: 20px; padding: 12px 18px;">
            <i class="uil uil-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid-2">
        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px; font-weight: 700; display: flex; align-items: center;">
                    <i class="uil uil-setting me-2" style="color: var(--primary);"></i> Core Configuration
                </h3>
            </div>
            <form action="{{ route('groups.settings.update') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Participants Per Group</label>
                    <input type="number" name="participants_per_group" class="form-control" value="{{ $settings->participants_per_group ?? 5 }}" min="1">
                    <small style="color: var(--text-muted); font-size: 11px;">The target number of students for each automatically formed group.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Formation Countdown (Minutes)</label>
                    <input type="number" name="countdown_minutes" class="form-control" value="{{ $settings->countdown_minutes ?? 60 }}" min="0">
                    <small style="color: var(--text-muted); font-size: 11px;">Time allowed for students to form groups manually before auto-formation kicks in.</small>
                </div>

                <div style="margin-top: 24px;">
                    <h3 style="font-size: 15px; font-weight: 600; margin-bottom: 15px;">Formation Rules</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="auto_create_groups" value="1" {{ ($settings->auto_create_groups ?? false) ? 'checked' : '' }} style="margin-right: 10px; width: 18px; height: 18px;">
                            <div>
                                <span style="font-size: 13px; font-weight: 600;">Enable Auto-Formation</span>
                                <p style="font-size: 11px; color: var(--text-muted); margin: 0;">Automatically create groups when the countdown expires.</p>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="balance_by_gender" value="1" {{ ($settings->balance_by_gender ?? false) ? 'checked' : '' }} style="margin-right: 10px; width: 18px; height: 18px;">
                            <div>
                                <span style="font-size: 13px; font-weight: 600;">Balance by Gender</span>
                                <p style="font-size: 11px; color: var(--text-muted); margin: 0;">Try to maintain an even distribution of genders in each group.</p>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="balance_by_skills" value="1" {{ ($settings->balance_by_skills ?? false) ? 'checked' : '' }} style="margin-right: 10px; width: 18px; height: 18px;">
                            <div>
                                <span style="font-size: 13px; font-weight: 600;">Balance by Skills</span>
                                <p style="font-size: 11px; color: var(--text-muted); margin: 0;">Distribute technical skills evenly based on student surveys.</p>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; cursor: pointer;">
                            <input type="checkbox" name="is_active" value="1" {{ ($settings->is_active ?? false) ? 'checked' : '' }} style="margin-right: 10px; width: 18px; height: 18px;">
                            <div>
                                <span style="font-size: 13px; font-weight: 600; color: var(--primary);">Activate Countdown</span>
                                <p style="font-size: 11px; color: var(--text-muted); margin: 0;">Start the timer for group formation.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary">
                        <i class="uil uil-save me-2"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 style="font-size: 16px; font-weight: 700; display: flex; align-items: center;">
                    <i class="uil uil-clock me-2" style="color: var(--secondary);"></i> Countdown Status
                </h3>
            </div>
            
                <div style="text-align: center; padding: 30px 0;">
                    <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Time Remaining</div>
                    <div id="countdown-timer" style="font-size: 48px; font-weight: 800; color: var(--text); font-family: monospace; background: var(--bg-alt); display: inline-block; padding: 10px 20px; border-radius: 12px; border: 1px solid var(--border);">
                        {{ $settings->formatted_remaining_time ?? '00:00:00' }}
                    </div>
                    
                    <div style="margin-top: 24px;">
                        <span id="countdown-status-badge" class="badge {{ $settings->isCountdownRunning() ? 'badge-green' : 'badge-gray' }}" style="padding: 6px 12px; font-size: 12px;">
                            <i class="uil {{ $settings->isCountdownRunning() ? 'uil-play' : 'uil-stop-circle' }} me-1"></i>
                            <span id="countdown-status-text">{{ $settings->isCountdownRunning() ? 'Countdown Active' : 'Countdown Inactive' }}</span>
                        </span>
                    </div>
                </div>

                <div style="margin-top: 20px; border-top: 1px solid var(--border); padding-top: 20px;">
                    <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">Quick Actions</h4>
                    <div style="display: grid; gap: 10px;">
                        <button type="button" class="btn btn-primary btn-sm" id="start-countdown-btn" style="width: 100%;" {{ $settings->isCountdownRunning() ? 'disabled' : '' }} onclick="startCountdown()">
                            <i class="uil uil-play me-2"></i> Start Countdown
                        </button>
                        <button type="button" class="btn btn-outline btn-sm" style="width: 100%; text-align: left; justify-content: flex-start; color: var(--danger); border-color: var(--danger-light);" onclick="triggerAutoFormation()">
                            <i class="uil uil-exclamation-triangle me-2"></i> Trigger Auto-Formation Now
                        </button>
                    </div>
                </div>
        </div>
    </div>
</div>
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
