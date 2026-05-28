@extends('layouts.app')

@section('breadcrumb', 'Admin Control')

@section('content')
<div class="page active" id="page-admin-control">
    <div class="section-header">
        <div>
            <div class="section-title">System Control</div>
            <div class="section-sub">Advanced controls for login, password reset, and system messaging</div>
        </div>
    </div>

    @if(session('status'))
        <div class="card" style="padding:12px 14px;margin-bottom:14px;border:1px solid rgba(16,185,129,.35);background:rgba(16,185,129,.10)">
            <div style="font-weight:800;margin-bottom:2px">Done</div>
            <div style="color:var(--text-muted)">{{ session('status') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="card" style="padding:12px 14px;margin-bottom:14px;border:1px solid rgba(255,80,120,.35);background:rgba(255,80,120,.10)">
            <div style="font-weight:800;margin-bottom:2px">Action failed</div>
            <div style="color:var(--text-muted)">{{ $errors->first() }}</div>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:14px;align-items:start">
        <div class="card" style="padding:16px">
            <div style="font-weight:900;font-size:15px;margin-bottom:6px">Access Controls</div>
            <div style="color:var(--text-muted);font-size:13px;margin-bottom:14px">Enable or disable public authentication pages.</div>

            <form method="POST" action="{{ route('admin.control.update') }}">
                @csrf

                <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:10px 0;border-bottom:1px solid var(--border)">
                    <div>
                        <div style="font-weight:800">Login Page</div>
                        <div style="color:var(--text-muted);font-size:12.5px">Allow users to access the login form</div>
                    </div>
                    <div>
                        <input type="hidden" name="login_enabled" value="0">
                        <input type="checkbox" name="login_enabled" value="1" {{ ($settings['login_enabled'] ?? true) ? 'checked' : '' }}>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:10px 0;border-bottom:1px solid var(--border)">
                    <div>
                        <div style="font-weight:800">Password Reset</div>
                        <div style="color:var(--text-muted);font-size:12.5px">Allow OTP reset flow (forgot/reset pages)</div>
                    </div>
                    <div>
                        <input type="hidden" name="password_reset_enabled" value="0">
                        <input type="checkbox" name="password_reset_enabled" value="1" {{ ($settings['password_reset_enabled'] ?? true) ? 'checked' : '' }}>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:10px 0">
                    <div>
                        <div style="font-weight:800">Registration</div>
                        <div style="color:var(--text-muted);font-size:12.5px">Allow new users to register</div>
                    </div>
                    <div>
                        <input type="hidden" name="registration_enabled" value="0">
                        <input type="checkbox" name="registration_enabled" value="1" {{ ($settings['registration_enabled'] ?? true) ? 'checked' : '' }}>
                    </div>
                </div>

                <div style="height:12px"></div>
                <div style="font-weight:900;font-size:14px;margin-bottom:6px">Notification Controls</div>
                <div style="color:var(--text-muted);font-size:13px;margin-bottom:10px">Enable or disable outbound SMS and Email across the system.</div>

                <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:10px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border)">
                    <div>
                        <div style="font-weight:800">Email Sending</div>
                        <div style="color:var(--text-muted);font-size:12.5px">Allow system emails (welcome, group formed, broadcasts)</div>
                    </div>
                    <div>
                        <input type="hidden" name="email_enabled" value="0">
                        <input type="checkbox" name="email_enabled" value="1" {{ ($settings['email_enabled'] ?? true) ? 'checked' : '' }}>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;padding:10px 0">
                    <div>
                        <div style="font-weight:800">SMS Sending</div>
                        <div style="color:var(--text-muted);font-size:12.5px">Allow system SMS (welcome, group formed, broadcasts)</div>
                    </div>
                    <div>
                        <input type="hidden" name="sms_enabled" value="0">
                        <input type="checkbox" name="sms_enabled" value="1" {{ ($settings['sms_enabled'] ?? true) ? 'checked' : '' }}>
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:14px">
                    <button class="btn btn-primary btn-sm" type="submit"><i class="uil uil-save me-1"></i> Save</button>
                </div>
            </form>
        </div>

        <div class="card" style="padding:16px">
            <div style="font-weight:900;font-size:15px;margin-bottom:6px">Queue Status</div>
            <div style="color:var(--text-muted);font-size:13px;margin-bottom:14px">SMS and email are sent by queued jobs. The queue worker must be running.</div>

            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px">
                <span class="badge badge-blue"><i class="uil uil-server me-1"></i> Pending jobs: {{ (int) ($jobsPending ?? 0) }}</span>
                <span class="badge {{ ((int) ($failedJobs ?? 0)) > 0 ? 'badge-red' : 'badge-green' }}"><i class="uil uil-bug me-1"></i> Failed jobs: {{ (int) ($failedJobs ?? 0) }}</span>
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <form method="POST" action="{{ route('admin.control.start_queue_worker') }}">
                    @csrf
                    <button class="btn btn-outline btn-sm" type="submit"><i class="uil uil-rocket me-1"></i> Start Queue Worker</button>
                </form>
                <form method="POST" action="{{ route('admin.control.process_queue') }}">
                    @csrf
                    <button class="btn btn-primary btn-sm" type="submit"><i class="uil uil-play me-1"></i> Process Queue Now</button>
                </form>
            </div>
        </div>

        <div class="card" style="padding:16px">
            <div style="font-weight:900;font-size:15px;margin-bottom:6px">Send Email</div>
            <div style="color:var(--text-muted);font-size:13px;margin-bottom:14px">Broadcast an email message to system users.</div>

            <form method="POST" action="{{ route('admin.control.email') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Audience</label>
                    <select class="form-control" name="audience" required>
                        <option value="all">All active users</option>
                        <option value="student">Students</option>
                        <option value="supervisor">Supervisors</option>
                        <option value="admin">Admins</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <input class="form-control" name="subject" required maxlength="160" placeholder="Subject">
                </div>
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea class="form-control" name="message" required rows="5" maxlength="4000" placeholder="Write your message"></textarea>
                </div>
                <button class="btn btn-primary btn-sm" type="submit"><i class="uil uil-envelope-send me-1"></i> Send Email</button>
            </form>
        </div>

        <div class="card" style="padding:16px">
            <div style="font-weight:900;font-size:15px;margin-bottom:6px">Send SMS</div>
            <div style="color:var(--text-muted);font-size:13px;margin-bottom:14px">Broadcast an SMS message to users with a phone number.</div>

            <form method="POST" action="{{ route('admin.control.sms') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Audience</label>
                    <select class="form-control" name="audience" required>
                        <option value="all">All active users</option>
                        <option value="student">Students</option>
                        <option value="supervisor">Supervisors</option>
                        <option value="admin">Admins</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea class="form-control" name="message" required rows="4" maxlength="480" placeholder="Write your SMS (max 480 chars)"></textarea>
                </div>
                <button class="btn btn-primary btn-sm" type="submit"><i class="uil uil-message me-1"></i> Send SMS</button>
            </form>
        </div>

        <div class="card" style="padding:16px">
            <div style="font-weight:900;font-size:15px;margin-bottom:6px">Test SMS</div>
            <div style="color:var(--text-muted);font-size:13px;margin-bottom:14px">
                Send a test message to any number format: 255XXXXXXXXX, 0XXXXXXXXX, or XXXXXXXXX.
            </div>

            <form method="POST" action="{{ route('admin.control.sms_test') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input class="form-control" name="phone" required maxlength="32" placeholder="e.g. 0612876654 or 255612876654">
                </div>
                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea class="form-control" name="message" required rows="4" maxlength="480" placeholder="Type your test SMS (max 480 chars)"></textarea>
                </div>
                <button class="btn btn-primary btn-sm" type="submit"><i class="uil uil-comment-alt-message me-1"></i> Send Test SMS</button>
            </form>
        </div>

        <div class="card" style="padding:16px">
            <div style="font-weight:900;font-size:15px;margin-bottom:6px">System Tools</div>
            <div style="color:var(--text-muted);font-size:13px;margin-bottom:14px">Maintenance utilities for administrators.</div>

            <form method="POST" action="{{ route('admin.control.cache_clear') }}">
                @csrf
                <button class="btn btn-outline btn-sm" type="submit"><i class="uil uil-refresh me-1"></i> Clear Cache</button>
            </form>
        </div>
    </div>
</div>
@endsection
