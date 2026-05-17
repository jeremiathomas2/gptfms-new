@extends('layouts.app')

@section('breadcrumb', 'My Group')

@section('content')
<div class="page active" id="page-my-group">
    <div class="section-header">
        <div>
            <div class="section-title">My Project Group</div>
            <div class="section-sub">View your team members and supervisor details</div>
        </div>
    </div>

    @if(!$group)
        <div class="card" style="text-align: center; padding: 60px 20px;">
            <div style="font-size: 48px; color: var(--text-muted); margin-bottom: 20px;">
                <i class="uil uil-users-alt"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 10px;">No Group Assigned</h3>
            <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto 25px;">
                You haven't been assigned to any project group yet. Groups will be formed automatically once the formation period ends.
            </p>
            <a href="{{ route('survey.index') }}" class="btn btn-primary">
                <i class="uil uil-clipboard-notes me-1"></i> Complete Skills Survey
            </a>
        </div>
    @else
        <div class="grid-3">
            <!-- Group Info -->
            <div class="card col-span-2">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 18px; font-weight: 800;">{{ $group->name }}</h3>
                    <span class="badge badge-green">ACTIVE</span>
                </div>
                
                <div style="margin-bottom: 25px;">
                    <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Project Topic</div>
                    <div style="font-size: 15px; font-weight: 700; color: var(--primary);">{{ $group->project->title ?? 'TBD' }}</div>
                    <div style="font-size: 13px; color: var(--text-muted); margin-top: 5px;">{{ $group->project->course_code ?? '' }}</div>
                </div>

                <div style="margin-bottom: 25px;">
                    <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">Team Members</div>
                    <div style="display: grid; gap: 12px;">
                        @foreach($group->members as $member)
                            <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg-alt); border-radius: 12px; border: 1px solid var(--border);">
                                <div class="sidebar-avatar" style="width: 40px; height: 40px; background: {{ $member->user->avatar ? 'url('.asset($member->user->avatar).') center/cover' : 'var(--primary)' }}; display: flex; align-items: center; justify-content: center; color: #fff;">
                                    {{ $member->user->avatar ? '' : $member->user->initials }}
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-size: 14px; font-weight: 700;">{{ $member->user->name }} @if($member->user_id == auth()->id()) <span style="font-weight: 400; color: var(--primary); font-size: 11px;">(You)</span> @endif</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">{{ $member->user->registration_number }} · {{ ucfirst($member->role) }}</div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 12px; font-weight: 600;">{{ $member->user->email }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">{{ $member->user->phone ?? 'No phone' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Supervisor & Actions -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="card">
                    <div class="card-header">
                        <h3 style="font-size: 15px; font-weight: 700;">Project Supervisor</h3>
                    </div>
                    @if($group->supervisor)
                        <div style="text-align: center; padding: 10px 0;">
                            <div class="sidebar-avatar" style="width: 64px; height: 64px; font-size: 24px; margin: 0 auto 15px; background: {{ $group->supervisor->avatar ? 'url('.asset($group->supervisor->avatar).') center/cover' : 'var(--secondary)' }}; display: flex; align-items: center; justify-content: center; color: #fff; border-radius: 16px;">
                                {{ $group->supervisor->avatar ? '' : $group->supervisor->initials }}
                            </div>
                            <div style="font-size: 16px; font-weight: 800;">{{ $group->supervisor->name }}</div>
                            <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 15px;">Academic Supervisor</div>
                            
                            <div style="display: grid; gap: 8px; text-align: left;">
                                <a href="mailto:{{ $group->supervisor->email }}" class="btn btn-outline btn-sm" style="width: 100%; justify-content: flex-start;">
                                    <i class="uil uil-envelope me-2"></i> {{ $group->supervisor->email }}
                                </a>
                                <div class="btn btn-outline btn-sm" style="width: 100%; justify-content: flex-start; cursor: default;">
                                    <i class="uil uil-phone me-2"></i> {{ $group->supervisor->phone ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="text-align: center; padding: 20px 0; color: var(--text-muted);">
                            <i class="uil uil-user-exclamation" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
                            <div style="font-size: 13px;">No supervisor assigned yet.</div>
                        </div>
                    @endif
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 style="font-size: 15px; font-weight: 700;">Quick Actions</h3>
                    </div>
                    <div style="display: grid; gap: 10px;">
                        <a href="{{ route('messages') }}?type=group&id={{ $group->id }}" class="btn btn-primary btn-sm" style="width: 100%;">
                            <i class="uil uil-comments me-2"></i> Group Chat
                        </a>
                        <a href="{{ route('tasks') }}" class="btn btn-outline btn-sm" style="width: 100%;">
                            <i class="uil uil-check-circle me-2"></i> View Tasks
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
