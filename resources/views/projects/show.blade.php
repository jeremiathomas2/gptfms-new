@extends('layouts.app')

@section('breadcrumb')
    @if(($mode ?? 'student') === 'student')
        Project
    @else
        Project
    @endif
@endsection

@section('content')
@php
    $mode = $mode ?? 'student';
    $phaseMap = $phaseMap ?? \App\Models\Project::PHASES;
    $project = $project ?? null;
    $phases = $phases ?? collect();
    $tasks = $tasks ?? collect();
    $members = $members ?? ($group?->activeMembers?->pluck('user')->filter()->values() ?? collect());

    $isStudent = $mode === 'student';
    $isSupervisor = $mode === 'supervisor';
    $isAdmin = $mode === 'admin';

    $displayPhases = $phases;
    if (($displayPhases?->count() ?? 0) === 0) {
        $displayPhases = collect($phaseMap)->map(function ($title, $n) {
            return (object) [
                'phase_number' => (int) $n,
                'phase_title' => (string) $title,
                'status' => 'not_started',
                'submission' => null,
                'supervisor_notes' => null,
                'submitted_at' => null,
                'reviewed_at' => null,
                'submittedBy' => null,
                'reviewedBy' => null,
            ];
        })->values();
    }

    $phase1 = $displayPhases->firstWhere('phase_number', 1);
    $isTitleApproved = ($phase1 && ($phase1->status ?? null) === 'approved');
    $headerTitle = $isTitleApproved && $project?->title ? $project->title : 'Not approved Yet';
@endphp

<div class="page active" id="page-project-show">
    <div class="section-header">
        <div>
            <div class="section-title">
                {{ $headerTitle }}
            </div>
            <div class="section-sub">
                @if($project)
                    <span class="badge badge-gray"><i class="uil uil-users-alt me-1"></i> {{ $project->group?->name ?? 'Group' }}</span>
                    <span class="badge badge-blue"><i class="uil uil-graduation-cap me-1"></i> {{ $project->supervisor?->name ?? 'Supervisor' }}</span>
                    <span class="badge {{ $project->status === 'completed' ? 'badge-green' : 'badge-amber' }}"><i class="uil uil-folder me-1"></i> {{ str_replace('_', ' ', ucfirst($project->status ?? 'in_progress')) }}</span>
                @else
                    @if(!empty($group) && empty($group->supervisor_id))
                        Waiting for supervisor assignment.
                    @else
                        Waiting for supervisor approval.
                    @endif
                @endif
            </div>
        </div>

        @if($project)
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                <a class="btn btn-outline btn-sm" href="{{ route('tasks') }}"><i class="uil uil-check-circle me-1"></i> Tasks</a>
                <a class="btn btn-outline btn-sm" href="{{ route('messages') }}"><i class="uil uil-comment-dots me-1"></i> Messages</a>
                @if(!$isStudent)
                    <a class="btn btn-primary btn-sm" href="{{ route('projects') }}"><i class="uil uil-arrow-left me-1"></i> Back</a>
                @endif
            </div>
        @endif
    </div>

    @php
        $approvedCount = $displayPhases->where('status', 'approved')->count();
        $totalPhases = count($phaseMap);
        $percent = $totalPhases > 0 ? round(($approvedCount / $totalPhases) * 100) : 0;
        $currentPhase = min($approvedCount + 1, $totalPhases);
    @endphp

    @if(!$project)
        <div class="card" style="padding:16px;margin-bottom:14px">
            <div style="display:flex;gap:12px;align-items:flex-start">
                <div class="stat-icon si-amber" style="width:46px;height:46px"><i class="uil uil-info-circle"></i></div>
                <div>
                    <div style="font-weight:900;margin-bottom:4px">Stages available</div>
                    <div style="color:var(--text-muted)">
                        You can view all stages now. Submission becomes available after a supervisor is assigned and your project is activated.
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid-7030">
        <div>
            <div class="card" style="padding:16px;margin-bottom:14px">
                <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:10px">
                    <div style="font-weight:900">Phase Progress</div>
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                        <span class="badge badge-green"><i class="uil uil-check-circle me-1"></i> Approved: {{ $approvedCount }}/{{ $totalPhases }}</span>
                        <span class="badge badge-amber"><i class="uil uil-steps me-1"></i> Current: Phase {{ $currentPhase }}</span>
                    </div>
                </div>
                <div class="progress-bar" style="height:10px">
                    <div class="progress-fill" style="width:{{ $percent }}%"></div>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:8px;color:var(--text-muted);font-size:12.5px">
                    <span>{{ $percent }}% complete</span>
                    <span><i class="uil uil-calendar-alt me-1"></i> Deadline: {{ $project?->end_date ? $project->end_date->format('M d, Y') : 'N/A' }}</span>
                </div>
            </div>

            <div style="display:grid;gap:12px">
                @foreach($displayPhases as $phase)
                    @php
                        $n = (int) $phase->phase_number;
                        $title = $phaseMap[$n] ?? $phase->phase_title;

                        $locked = $isStudent && (!$project || $n > $currentPhase);
                        $badge = 'badge-gray';
                        if ($phase->status === 'approved') $badge = 'badge-green';
                        if ($phase->status === 'submitted') $badge = 'badge-blue';
                        if ($phase->status === 'changes_requested') $badge = 'badge-amber';

                        $canStudentSubmit = $isStudent && $project && !$locked && $phase->status !== 'approved';
                        $canSupervisorReview = $project && ($isSupervisor || $isAdmin) && in_array($phase->status, ['submitted', 'changes_requested'], true);
                    @endphp
                        <div class="card" style="padding:16px" id="phase-card-{{ $n }}">
                            <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:flex-start;margin-bottom:10px">
                                <div>
                                    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                                        <div style="font-weight:900">Phase {{ $n }}: {{ $title }}</div>
                                        <span class="badge {{ $badge }}">{{ str_replace('_', ' ', ucfirst($phase->status)) }}</span>
                                        @if($locked)
                                            <span class="badge badge-gray"><i class="uil uil-lock me-1"></i> Locked</span>
                                        @endif
                                    </div>
                                    @if($phase->submitted_at)
                                        <div style="color:var(--text-muted);font-size:12.5px;margin-top:4px">
                                            Submitted {{ $phase->submitted_at->diffForHumans() }} by {{ $phase->submittedBy?->name ?? 'Student' }}
                                        </div>
                                    @endif
                                    @if($phase->reviewed_at)
                                        <div style="color:var(--text-muted);font-size:12.5px;margin-top:2px">
                                            Reviewed {{ $phase->reviewed_at->diffForHumans() }} by {{ $phase->reviewedBy?->name ?? 'Supervisor' }}
                                        </div>
                                    @endif
                                </div>
                                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                                    @if(!$isStudent && $project)
                                        <a class="btn btn-outline btn-sm" href="{{ route('projects.show', $project) }}#phase-card-{{ $n }}"><i class="uil uil-eye me-1"></i> Preview</a>
                                    @endif
                                </div>
                            </div>

                            @if($phase->submission)
                                <div class="card" style="padding:12px;border:1px solid var(--border);box-shadow:none;margin-bottom:10px">
                                    <div style="font-weight:800;margin-bottom:6px">Student Submission</div>
                                    <div style="white-space:pre-wrap;color:var(--text);line-height:1.55">{{ $phase->submission }}</div>
                                </div>
                            @endif

                            @if($phase->supervisor_notes)
                                <div class="card" style="padding:12px;border:1px solid var(--border);box-shadow:none;margin-bottom:10px;background:rgba(37,99,235,.06)">
                                    <div style="font-weight:800;margin-bottom:6px"><i class="uil uil-comment-alt-lines me-1"></i> Supervisor Notes</div>
                                    <div style="white-space:pre-wrap;color:var(--text);line-height:1.55">{{ $phase->supervisor_notes }}</div>
                                </div>
                            @endif

                            @if($canStudentSubmit)
                                <form class="phase-submit-form" data-project="{{ $project->id }}" data-phase="{{ $n }}">
                                    @csrf
                                    <div class="form-group" style="margin-bottom:10px">
                                        <label class="form-label">Your submission</label>
                                        <textarea class="form-control" name="submission" rows="6" maxlength="20000" placeholder="Write your phase work here..." required>{{ $phase->status === 'changes_requested' ? ($phase->submission ?? '') : ($phase->submission ?? '') }}</textarea>
                                    </div>
                                    <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap">
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="uil uil-message me-1"></i> Submit for Approval</button>
                                    </div>
                                </form>
                            @endif

                            @if($canSupervisorReview)
                                <div style="display:grid;grid-template-columns:1fr;gap:10px;margin-top:10px">
                                    <form class="phase-review-form" data-project="{{ $project->id }}" data-phase="{{ $n }}">
                                        @csrf
                                        <div class="form-group">
                                            <label class="form-label">Supervisor feedback / instructions</label>
                                            <textarea class="form-control" name="supervisor_notes" rows="4" maxlength="20000" placeholder="Provide feedback or required improvements...">{{ $phase->supervisor_notes }}</textarea>
                                        </div>
                                        <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap;margin-top:10px">
                                            <button type="button" class="btn btn-outline btn-sm phase-review-btn" data-action="changes"><i class="uil uil-edit me-1"></i> Request Changes</button>
                                            <button type="button" class="btn btn-primary btn-sm phase-review-btn" data-action="approve"><i class="uil uil-check me-1"></i> Approve Phase</button>
                                        </div>
                                    </form>

                                    <form class="phase-task-form" data-project="{{ $project->id }}" data-phase="{{ $n }}">
                                        @csrf
                                        <div style="font-weight:900;margin-top:6px">Assign Task for this Phase</div>
                                        <div class="form-row" style="margin-top:10px">
                                            <div class="form-group">
                                                <label class="form-label">Assign To</label>
                                                <select class="form-control" name="assigned_to" required>
                                                    <option value="" disabled selected>Select student</option>
                                                    @foreach(($members ?? collect()) as $m)
                                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Priority</label>
                                                <select class="form-control" name="priority" required>
                                                    <option value="low">Low</option>
                                                    <option value="medium" selected>Medium</option>
                                                    <option value="high">High</option>
                                                    <option value="urgent">Urgent</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label class="form-label">Task Title</label>
                                                <input class="form-control" name="title" required maxlength="255" placeholder="e.g. Improve requirements list" />
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Due Date</label>
                                                <input class="form-control" name="due_date" type="date" required />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="3" maxlength="5000" placeholder="Task details..."></textarea>
                                        </div>
                                        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:10px">
                                            <button type="submit" class="btn btn-outline btn-sm"><i class="uil uil-plus me-1"></i> Create Task</button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                @endforeach
            </div>
        </div>

        <div>
            <div class="card" style="padding:16px;margin-bottom:14px">
                <div class="section-title" style="font-size:14px;margin-bottom:12px"><i class="uil uil-users-alt me-2"></i> Team</div>
                <div style="display:grid;gap:10px">
                    @foreach(($members ?? collect()) as $m)
                        <div class="task-card" style="padding:12px">
                            <div class="task-title" style="display:flex;align-items:center;gap:10px">
                                <span class="av" style="width:34px;height:34px;border-radius:12px;background:var(--primary);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-weight:900">{{ $m->initials }}</span>
                                <div style="display:flex;flex-direction:column">
                                    <span style="font-weight:900">{{ $m->name }}</span>
                                    <span style="color:var(--text-muted);font-size:12px">{{ $m->email }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if(($members ?? collect())->isEmpty())
                        <div style="color:var(--text-muted)">No members found.</div>
                    @endif
                </div>
            </div>

            <div class="card" style="padding:16px">
                <div class="section-title" style="font-size:14px;margin-bottom:12px"><i class="uil uil-check-circle me-2"></i> Tasks</div>
                <div style="display:grid;gap:10px">
                    @forelse(($tasks ?? collect())->take(10) as $t)
                        <div class="task-card" style="padding:12px">
                            <div class="task-title" style="display:flex;justify-content:space-between;gap:10px">
                                <span>{{ $t->title }}</span>
                                <span class="badge badge-gray">{{ str_replace('_',' ', ucfirst($t->status)) }}</span>
                            </div>
                            <div class="task-meta" style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap">
                                <span class="badge badge-blue">{{ ucfirst($t->priority) }}</span>
                                <span style="color:var(--text-muted);font-size:12.5px"><i class="uil uil-calendar-alt me-1"></i>{{ $t->due_date ? $t->due_date->format('M d, Y') : 'No due date' }}</span>
                            </div>
                            @if($t->description)
                                <div style="color:var(--text-muted);margin-top:6px;white-space:pre-wrap">{{ $t->description }}</div>
                            @endif
                        </div>
                    @empty
                        <div style="color:var(--text-muted)">{{ $project ? 'No tasks yet.' : 'Tasks will appear after your project is activated.' }}</div>
                    @endforelse
                </div>
                @if(($tasks ?? collect())->count() > 10)
                    <div style="margin-top:10px">
                        <a class="btn btn-outline btn-sm" href="{{ route('tasks') }}">View all tasks</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  const tokenEl = document.querySelector('meta[name="csrf-token"]');
  const csrf = tokenEl ? tokenEl.getAttribute('content') : '';

  function postJson(url, payload) {
    return fetch(url, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify(payload || {})
    }).then(async (res) => {
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw data;
      return data;
    });
  }

  document.querySelectorAll('.phase-submit-form').forEach((form) => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const projectId = form.getAttribute('data-project');
      const phase = form.getAttribute('data-phase');
      const submission = (form.querySelector('[name="submission"]')?.value || '').trim();
      if (!submission) return;
      postJson(`/projects/${projectId}/phases/${phase}/submit`, { submission })
        .then((data) => {
          toast(data.message || 'Submitted', '<i class="uil uil-check"></i>');
          setTimeout(() => window.location.reload(), 700);
        })
        .catch((err) => {
          toast((err && (err.message || err.error)) || 'Failed to submit phase', '<i class="uil uil-exclamation-triangle"></i>');
        });
    });
  });

  document.querySelectorAll('.phase-review-form').forEach((form) => {
    form.querySelectorAll('.phase-review-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        const projectId = form.getAttribute('data-project');
        const phase = form.getAttribute('data-phase');
        const supervisor_notes = (form.querySelector('[name="supervisor_notes"]')?.value || '').trim();
        const action = btn.getAttribute('data-action') === 'approve' ? 'approve' : 'changes';
        postJson(`/projects/${projectId}/phases/${phase}/review`, { action, supervisor_notes })
          .then((data) => {
            toast(data.message || 'Updated', '<i class="uil uil-check"></i>');
            setTimeout(() => window.location.reload(), 700);
          })
          .catch((err) => {
            toast((err && (err.message || err.error)) || 'Failed to review phase', '<i class="uil uil-exclamation-triangle"></i>');
          });
      });
    });
  });

  document.querySelectorAll('.phase-task-form').forEach((form) => {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const projectId = form.getAttribute('data-project');
      const phase = form.getAttribute('data-phase');
      const payload = {
        assigned_to: form.querySelector('[name="assigned_to"]')?.value,
        priority: form.querySelector('[name="priority"]')?.value,
        title: (form.querySelector('[name="title"]')?.value || '').trim(),
        due_date: form.querySelector('[name="due_date"]')?.value,
        description: (form.querySelector('[name="description"]')?.value || '').trim()
      };
      if (!payload.title || !payload.assigned_to || !payload.due_date || !payload.priority) return;
      postJson(`/projects/${projectId}/phases/${phase}/tasks`, payload)
        .then((data) => {
          toast(data.message || 'Task created', '<i class="uil uil-check"></i>');
          setTimeout(() => window.location.reload(), 700);
        })
        .catch((err) => {
          toast((err && (err.message || err.error)) || 'Failed to create task', '<i class="uil uil-exclamation-triangle"></i>');
        });
    });
  });
})();
</script>
@endpush
