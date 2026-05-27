@extends('layouts.app')

@section('breadcrumb', 'Tasks')

@section('content')
@php
    $isStudent = auth()->user()?->hasRole('student') ?? false;
@endphp
<div class="page active" id="page-tasks">
    <div class="section-header">
        <div><div class="section-title">Task Board</div><div class="section-sub">Manage your workflow across teams</div></div>
        <div style="display:flex;gap:8px">
            <select class="form-control" style="max-width:160px;padding:7px 12px;font-size:12.5px"><option>All Groups</option></select>
            @if(!empty($canCreate))
                <button class="btn btn-primary btn-sm" onclick="openModal('modal-task')"><i class="uil uil-plus me-1"></i> Add Task</button>
            @endif
        </div>
    </div>
    <div class="kanban" style="grid-template-columns:repeat(3,1fr)">
        <div class="kanban-col">
            <div class="kanban-col-header"><span class="kanban-col-title"><i class="uil uil-list-ul me-2"></i> To Do</span><span class="kanban-count">{{ $todoTasks->count() }}</span></div>
            @foreach($todoTasks as $task)
            <div class="task-card js-task"
                 data-id="{{ $task->id }}"
                 data-title="{{ $task->title }}"
                 data-description="{{ (string) ($task->description ?? '') }}"
                 data-project="{{ (string) ($task->project?->title ?? '') }}"
                 data-group="{{ (string) ($task->project?->group?->name ?? '') }}"
                 data-priority="{{ (string) ($task->priority ?? '') }}"
                 data-status="{{ (string) ($task->status ?? '') }}"
                 data-due="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}"
                 data-assignee="{{ (string) ($task->assignee?->name ?? '') }}"
                 data-creator="{{ (string) ($task->creator?->name ?? '') }}">
                <div class="task-title">{{ $task->title }}</div>
                <div style="margin-bottom:6px">
                    <span class="badge {{ $task->priority === 'high' ? 'badge-red' : ($task->priority === 'medium' ? 'badge-amber' : 'badge-blue') }}">
                        {{ ucfirst($task->priority) }} Priority
                    </span>
                </div>
                <div class="task-meta">
                    <div class="avatar-group">
                        @if($task->assignee)
                        <div class="av" title="{{ $task->assignee->name }}">{{ $task->assignee->initials }}</div>
                        @else
                        <div class="av" title="Unassigned"><i class="uil uil-user-exclamation"></i></div>
                        @endif
                    </div>
                    <div class="task-due {{ $task->isOverdue() ? 'color-danger' : '' }}">
                        <i class="uil uil-calendar-alt me-1"></i> {{ $task->due_date ? $task->due_date->format('M d') : 'No date' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="kanban-col">
            <div class="kanban-col-header"><span class="kanban-col-title"><i class="uil uil-spinner me-2"></i> In Progress</span><span class="kanban-count">{{ $inProgressTasks->count() }}</span></div>
            @foreach($inProgressTasks as $task)
            <div class="task-card js-task" style="border-left:3px solid var(--primary)"
                 data-id="{{ $task->id }}"
                 data-title="{{ $task->title }}"
                 data-description="{{ (string) ($task->description ?? '') }}"
                 data-project="{{ (string) ($task->project?->title ?? '') }}"
                 data-group="{{ (string) ($task->project?->group?->name ?? '') }}"
                 data-priority="{{ (string) ($task->priority ?? '') }}"
                 data-status="{{ (string) ($task->status ?? '') }}"
                 data-due="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}"
                 data-assignee="{{ (string) ($task->assignee?->name ?? '') }}"
                 data-creator="{{ (string) ($task->creator?->name ?? '') }}">
                <div class="task-title">{{ $task->title }}</div>
                <div style="margin-bottom:6px">
                    <span class="badge badge-blue">In Progress</span>
                </div>
                <div class="progress-wrap" style="margin-bottom:8px">
                    <div class="progress-bar"><div class="progress-fill" style="width:{{ $task->progress ?? 50 }}%"></div></div>
                </div>
                <div class="task-meta">
                    <div class="avatar-group">
                        @if($task->assignee)
                        <div class="av" title="{{ $task->assignee->name }}">{{ $task->assignee->initials }}</div>
                        @endif
                    </div>
                    <div class="task-due">
                        <i class="uil uil-calendar-alt me-1"></i> {{ $task->due_date ? $task->due_date->format('M d') : '' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="kanban-col">
            <div class="kanban-col-header"><span class="kanban-col-title"><i class="uil uil-check-circle me-2"></i> Completed</span><span class="kanban-count">{{ $completedTasks->count() }}</span></div>
            @foreach($completedTasks as $task)
            <div class="task-card js-task" style="opacity:.6"
                 data-id="{{ $task->id }}"
                 data-title="{{ $task->title }}"
                 data-description="{{ (string) ($task->description ?? '') }}"
                 data-project="{{ (string) ($task->project?->title ?? '') }}"
                 data-group="{{ (string) ($task->project?->group?->name ?? '') }}"
                 data-priority="{{ (string) ($task->priority ?? '') }}"
                 data-status="{{ (string) ($task->status ?? '') }}"
                 data-due="{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}"
                 data-assignee="{{ (string) ($task->assignee?->name ?? '') }}"
                 data-creator="{{ (string) ($task->creator?->name ?? '') }}">
                <div class="task-title">{{ $task->title }}</div>
                <div class="task-meta">
                    <span class="badge badge-green">Done</span>
                    <div class="task-due">
                        <i class="uil uil-calendar-alt me-1"></i> {{ $task->completed_at ? $task->completed_at->format('M d') : '' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="modal-overlay" id="modal-task-preview">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title" id="taskPreviewTitle">Task</span>
            <span class="modal-close" onclick="closeModal('modal-task-preview')"><i class="uil uil-multiply"></i></span>
        </div>
        <div style="display:grid;gap:12px">
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center" id="taskPreviewBadges"></div>
            <div class="card" style="padding:12px;border:1px solid var(--border);box-shadow:none">
                <div style="font-weight:900;margin-bottom:6px">Details</div>
                <div style="display:grid;gap:6px;color:var(--text)">
                    <div><span style="color:var(--text-muted)">Project:</span> <span id="taskPreviewProject">—</span></div>
                    <div><span style="color:var(--text-muted)">Group:</span> <span id="taskPreviewGroup">—</span></div>
                    <div><span style="color:var(--text-muted)">Assigned to:</span> <span id="taskPreviewAssignee">—</span></div>
                    <div><span style="color:var(--text-muted)">Created by:</span> <span id="taskPreviewCreator">—</span></div>
                    <div><span style="color:var(--text-muted)">Due date:</span> <span id="taskPreviewDue">—</span></div>
                </div>
            </div>
            <div class="card" style="padding:12px;border:1px solid var(--border);box-shadow:none">
                <div style="font-weight:900;margin-bottom:6px">Description</div>
                <div id="taskPreviewDescription" style="white-space:pre-wrap;color:var(--text);line-height:1.55">—</div>
            </div>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:14px;flex-wrap:wrap">
            <button type="button" class="btn btn-outline" onclick="closeModal('modal-task-preview')">Close</button>
            @if($isStudent)
                <button type="button" class="btn btn-outline" id="taskPreviewAcceptBtn"><i class="uil uil-play me-1"></i> Accept</button>
                <button type="button" class="btn btn-primary" id="taskPreviewCompleteBtn"><i class="uil uil-check me-1"></i> Mark as Readed</button>
            @endif
        </div>
    </div>
</div>

<!-- ═══════════════ ADD TASK MODAL ═══════════════ -->
@if(!empty($canCreate))
    <div class="modal-overlay" id="modal-task">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title">Add New Task</span>
                <span class="modal-close" onclick="closeModal('modal-task')"><i class="uil uil-multiply"></i></span>
            </div>
            <form id="addTaskForm">
                @csrf
                <div class="form-group">
                    <label class="form-label">Task Title</label>
                    <input name="title" class="form-control" placeholder="e.g. Improve requirements" required />
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-control" required>
                            @foreach(($projects ?? collect()) as $project)
                                <option value="{{ $project->id }}">{{ $project->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-control" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Assign To</label>
                        <select name="assigned_to" class="form-control">
                            <option value="">Unassigned</option>
                            @foreach(($assignees ?? collect()) as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Due Date</label>
                        <input name="due_date" type="date" class="form-control" required />
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Task details..."></textarea>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:15px;">
                    <button type="button" class="btn btn-outline" onclick="closeModal('modal-task')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('addTaskForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("{{ route('tasks.store') }}", {
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
                closeModal('modal-task');
                setTimeout(() => window.location.reload(), 900);
            } else {
                toast(data.message || 'Error creating task', '<i class="uil uil-exclamation-triangle"></i>');
            }
        })
        .catch(() => {
            toast('An error occurred. Please try again.', '<i class="uil uil-exclamation-triangle"></i>');
        });
    });
    </script>
@endif

@push('scripts')
<script>
(function () {
    const tokenEl = document.querySelector('meta[name="csrf-token"]');
    const csrf = tokenEl ? tokenEl.getAttribute('content') : '';
    const isStudent = {{ $isStudent ? 'true' : 'false' }};

    const els = {
        title: document.getElementById('taskPreviewTitle'),
        badges: document.getElementById('taskPreviewBadges'),
        project: document.getElementById('taskPreviewProject'),
        group: document.getElementById('taskPreviewGroup'),
        assignee: document.getElementById('taskPreviewAssignee'),
        creator: document.getElementById('taskPreviewCreator'),
        due: document.getElementById('taskPreviewDue'),
        desc: document.getElementById('taskPreviewDescription'),
        acceptBtn: document.getElementById('taskPreviewAcceptBtn'),
        completeBtn: document.getElementById('taskPreviewCompleteBtn'),
    };

    let currentTaskId = null;
    let currentStatus = null;

    function post(url) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            }
        }).then(async (res) => {
            const data = await res.json().catch(() => ({}));
            if (!res.ok) throw data;
            return data;
        });
    }

    function titleize(s) {
        return String(s || '').replace(/_/g, ' ').replace(/\b\w/g, (m) => m.toUpperCase());
    }

    function badge(html, cls) {
        const span = document.createElement('span');
        span.className = `badge ${cls || 'badge-gray'}`;
        span.innerHTML = html;
        return span;
    }

    function setButtonsForStatus(status) {
        if (!isStudent) return;
        const canAccept = status === 'todo';
        const canComplete = status !== 'completed';

        if (els.acceptBtn) els.acceptBtn.style.display = canAccept ? '' : 'none';
        if (els.completeBtn) els.completeBtn.style.display = canComplete ? '' : 'none';
    }

    document.querySelectorAll('.js-task').forEach((card) => {
        card.addEventListener('click', () => {
            currentTaskId = card.getAttribute('data-id');
            const title = card.getAttribute('data-title') || 'Task';
            const description = card.getAttribute('data-description') || '';
            const project = card.getAttribute('data-project') || '—';
            const group = card.getAttribute('data-group') || '—';
            const priority = card.getAttribute('data-priority') || '';
            const status = card.getAttribute('data-status') || '';
            const due = card.getAttribute('data-due') || '';
            const assignee = card.getAttribute('data-assignee') || '—';
            const creator = card.getAttribute('data-creator') || '—';

            currentStatus = status;

            if (els.title) els.title.textContent = title;
            if (els.project) els.project.textContent = project || '—';
            if (els.group) els.group.textContent = group || '—';
            if (els.assignee) els.assignee.textContent = assignee || '—';
            if (els.creator) els.creator.textContent = creator || '—';
            if (els.due) els.due.textContent = due ? due : '—';
            if (els.desc) els.desc.textContent = description ? description : '—';

            if (els.badges) {
                els.badges.innerHTML = '';
                if (priority) {
                    const cls = priority === 'high' ? 'badge-red' : (priority === 'medium' ? 'badge-amber' : 'badge-blue');
                    els.badges.appendChild(badge(`${titleize(priority)} Priority`, cls));
                }
                if (status) {
                    const cls = status === 'completed' ? 'badge-green' : (status === 'in_progress' ? 'badge-blue' : (status === 'review' ? 'badge-amber' : 'badge-gray'));
                    els.badges.appendChild(badge(titleize(status), cls));
                }
            }

            setButtonsForStatus(status);
            openModal('modal-task-preview');
        });
    });

    els.acceptBtn?.addEventListener('click', () => {
        if (!currentTaskId) return;
        post(`/tasks/${currentTaskId}/accept`)
            .then((data) => {
                toast(data.message || 'Accepted', '<i class="uil uil-check"></i>');
                setTimeout(() => window.location.reload(), 700);
            })
            .catch((err) => {
                toast((err && (err.message || err.error)) || 'Failed to accept task', '<i class="uil uil-exclamation-triangle"></i>');
            });
    });

    els.completeBtn?.addEventListener('click', () => {
        if (!currentTaskId) return;
        post(`/tasks/${currentTaskId}/complete`)
            .then((data) => {
                toast(data.message || 'Completed', '<i class="uil uil-check"></i>');
                setTimeout(() => window.location.reload(), 700);
            })
            .catch((err) => {
                toast((err && (err.message || err.error)) || 'Failed to complete task', '<i class="uil uil-exclamation-triangle"></i>');
            });
    });
})();
</script>
@endpush
@endsection
